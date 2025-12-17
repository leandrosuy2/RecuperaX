<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PicPayService
{
    private string $clientId;
    private string $clientSecret;
    private string $apiUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->clientId = config('services.picpay.client_id');
        $this->clientSecret = config('services.picpay.client_secret');
        $this->apiUrl = config('services.picpay.api_url', 'https://api.picpay.com');
        $this->sandbox = config('services.picpay.sandbox', false);
    }

    /**
     * Configura o cliente HTTP com opções de SSL apropriadas
     */
    private function httpClient()
    {
        // Timeout maior para resolver problemas de DNS e conexão
        $client = Http::timeout(60)
            ->connectTimeout(30);

        // Em ambiente local/desenvolvimento, desabilitar verificação SSL se necessário
        if (app()->environment('local', 'development')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Obtém o token de autenticação OAuth 2.0
     */
    private function getAccessToken(): ?string
    {
        $cacheKey = 'picpay_access_token';

        // Tenta obter do cache primeiro
        $token = Cache::get($cacheKey);
        if ($token) {
            return $token;
        }

        try {
            $response = $this->httpClient()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/oauth2/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 3600;

                if ($token) {
                    // Cache o token por um tempo menor que o expires_in para garantir que não expire
                    Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 60));
                    return $token;
                }
            }

            Log::error('Erro ao obter token PicPay', [
                'status' => $response->status(),
                'response' => $response->body(),
                'url' => "{$this->apiUrl}/oauth2/token",
            ]);

            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $errorMessage = $e->getMessage();
            $isTimeout = str_contains($errorMessage, 'timed out') || str_contains($errorMessage, 'timeout');
            $isDns = str_contains($errorMessage, 'Resolving') || str_contains($errorMessage, 'DNS');
            
            Log::error('Erro de conexão ao obter token PicPay', [
                'message' => $errorMessage,
                'url' => "{$this->apiUrl}/oauth2/token",
                'is_timeout' => $isTimeout,
                'is_dns' => $isDns,
                'suggestion' => $isDns 
                    ? 'Erro de DNS: Verifique sua conexão com a internet e configurações de DNS'
                    : ($isTimeout 
                        ? 'Timeout: A conexão com o servidor PicPay está demorando muito. Verifique sua conexão.'
                        : 'Verifique sua conexão com a internet e se a URL da API está correta'),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao obter token PicPay', [
                'message' => $e->getMessage(),
                'type' => get_class($e),
                'url' => "{$this->apiUrl}/oauth2/token",
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Cria um pagamento no PicPay
     */
    public function criarPagamento(array $dados): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Erro ao autenticar com PicPay',
            ];
        }

        try {
            // Converter valor para centavos (a API espera valores em centavos)
            $valorCentavos = (int) round($dados['valor'] * 100);
            
            // Se CREDIT_CARD estiver nos métodos, garantir valor mínimo de R$ 5,00 (500 centavos)
            $paymentMethods = $dados['payment_methods'] ?? ['BRCODE', 'CREDIT_CARD'];
            $hasCreditCard = in_array('CREDIT_CARD', $paymentMethods);
            
            if ($hasCreditCard && $valorCentavos < 500) {
                return [
                    'success' => false,
                    'message' => 'Valor mínimo de R$ 5,00 é necessário quando cartão de crédito está habilitado.',
                ];
            }
            
            // Montar options - card_max_installment_number só é permitido quando há CREDIT_CARD
            $options = [
                'allow_create_pix_key' => $dados['allow_create_pix_key'] ?? true,
                'expired_at' => isset($dados['expires_at']) 
                    ? (is_string($dados['expires_at']) ? $dados['expires_at'] : $dados['expires_at']->format('Y-m-d'))
                    : now()->addDays(30)->format('Y-m-d'),
            ];
            
            // Só adicionar card_max_installment_number se CREDIT_CARD estiver nos métodos
            if ($hasCreditCard) {
                $options['card_max_installment_number'] = $dados['card_max_installment_number'] ?? 12;
            }
            
            // Garantir que charge_name não exceda 50 caracteres
            $chargeName = $dados['charge_name'] ?? 'Pagamento ' . $dados['reference_id'];
            $chargeName = mb_substr($chargeName, 0, 50);
            
            $payload = [
                'charge' => [
                    'name' => $chargeName,
                    'description' => $dados['charge_description'] ?? 'Pagamento via PicPay',
                    'order_number' => $dados['reference_id'],
                    'redirect_url' => $dados['return_url'] ?? $dados['callback_url'],
                    'payment' => [
                        'methods' => $paymentMethods,
                        'brcode_arrangements' => $dados['brcode_arrangements'] ?? ['PICPAY', 'PIX'],
                    ],
                    'amounts' => [
                        'product' => $valorCentavos,
                        'delivery' => $dados['delivery_amount'] ?? 0,
                    ],
                ],
                'options' => $options,
            ];

            $response = $this->httpClient()
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post("{$this->apiUrl}/v1/paymentlink/create", $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log da resposta para debug
                Log::info('Resposta da API PicPay', ['data' => $data]);
                
                // A API retorna a estrutura de resposta do payment link
                // Pode estar em 'payment_link' ou na raiz
                $paymentLink = $data['payment_link'] ?? $data;
                
                // Extrair URL de pagamento (tentar vários campos possíveis)
                // Priorizar 'link' que é o campo correto da API
                $paymentUrl = $data['link'] 
                    ?? $paymentLink['link'] 
                    ?? $paymentLink['deeplink']
                    ?? $paymentLink['url'] 
                    ?? $paymentLink['payment_url'] 
                    ?? $paymentLink['checkout_url'] 
                    ?? $data['url']
                    ?? $data['payment_url']
                    ?? null;
                
                // Extrair brcode (código PIX) - a API retorna o brcode, não uma imagem
                // Priorizar campo direto da resposta
                $brcode = $data['brcode'] ?? $paymentLink['brcode'] ?? null;
                
                // Extrair QR Code (se vier como imagem base64)
                $qrcodeData = $paymentLink['qrcode'] ?? $data['qrcode'] ?? null;
                $qrcodeBase64 = null;
                $qrcodeContent = null;
                
                if (is_array($qrcodeData)) {
                    $qrcodeBase64Raw = $qrcodeData['base64'] 
                        ?? $qrcodeData['base64_image'] 
                        ?? $qrcodeData['image']
                        ?? null;
                    // Remover prefixo data:image se existir
                    if ($qrcodeBase64Raw && str_starts_with($qrcodeBase64Raw, 'data:image')) {
                        $qrcodeBase64 = preg_replace('/^data:image\/[^;]+;base64,/', '', $qrcodeBase64Raw);
                    } else {
                        $qrcodeBase64 = $qrcodeBase64Raw;
                    }
                    $qrcodeContent = $qrcodeData['content'] 
                        ?? $qrcodeData['url'] 
                        ?? $qrcodeData['link']
                        ?? null;
                } elseif (is_string($qrcodeData)) {
                    $qrcodeContent = $qrcodeData;
                }
                
                // Se não tiver QR Code em base64, mas tiver brcode ou link, vamos gerar usando API externa
                if (!$qrcodeBase64) {
                    $qrcodeData = $brcode ?? $paymentUrl;
                    if ($qrcodeData) {
                        // Usar API pública para gerar QR Code
                        try {
                            $qrcodeResponse = Http::timeout(10)->get('https://api.qrserver.com/v1/create-qr-code/', [
                                'size' => '300x300',
                                'data' => $qrcodeData,
                            ]);
                            
                            if ($qrcodeResponse->successful()) {
                                // Salvar apenas o base64 sem o prefixo data:image
                                $qrcodeBase64 = base64_encode($qrcodeResponse->body());
                            }
                        } catch (\Exception $e) {
                            Log::warning('Erro ao gerar QR Code', ['message' => $e->getMessage()]);
                        }
                    }
                }
                
                return [
                    'success' => true,
                    'data' => $data,
                    'payment_url' => $paymentUrl,
                    'qrcode' => $qrcodeContent ?? $brcode,
                    'qrcode_base64' => $qrcodeBase64,
                    'brcode' => $brcode,
                ];
            }

            $error = $response->json();
            Log::error('Erro ao criar pagamento PicPay', [
                'status' => $response->status(),
                'response' => $error,
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'message' => $error['message'] ?? 'Erro ao criar pagamento',
                'errors' => $error['errors'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao criar pagamento PicPay', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Consulta o status de um pagamento
     */
    public function consultarPagamento(string $referenceId): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Erro ao autenticar com PicPay',
            ];
        }

        try {
            $response = $this->httpClient()
                ->withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get("{$this->apiUrl}/v1/paymentlink/{$referenceId}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'status' => $data['status'] ?? null,
                ];
            }

            Log::error('Erro ao consultar pagamento PicPay', [
                'status' => $response->status(),
                'response' => $response->body(),
                'reference_id' => $referenceId,
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao consultar pagamento',
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar pagamento PicPay', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao consultar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cancela um pagamento
     */
    public function cancelarPagamento(string $referenceId, ?string $authorizationId = null): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Erro ao autenticar com PicPay',
            ];
        }

        try {
            $payload = [];
            if ($authorizationId) {
                $payload['authorizationId'] = $authorizationId;
            }

            $response = $this->httpClient()
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post("{$this->apiUrl}/v1/paymentlink/{$referenceId}/cancel", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            Log::error('Erro ao cancelar pagamento PicPay', [
                'status' => $response->status(),
                'response' => $response->body(),
                'reference_id' => $referenceId,
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao cancelar pagamento',
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao cancelar pagamento PicPay', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao cancelar pagamento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processa notificação/callback do PicPay
     */
    public function processarNotificacao(array $dados): array
    {
        try {
            $referenceId = $dados['referenceId'] ?? null;
            $authorizationId = $dados['authorizationId'] ?? null;

            if (!$referenceId) {
                return [
                    'success' => false,
                    'message' => 'ReferenceId não encontrado na notificação',
                ];
            }

            // Consulta o status atualizado
            $consulta = $this->consultarPagamento($referenceId);

            if (!$consulta['success']) {
                return $consulta;
            }

            return [
                'success' => true,
                'reference_id' => $referenceId,
                'authorization_id' => $authorizationId,
                'status' => $consulta['data']['status'] ?? null,
                'data' => $consulta['data'],
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao processar notificação PicPay', [
                'message' => $e->getMessage(),
                'dados' => $dados,
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao processar notificação: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Lista transações de um payment link específico
     */
    public function listarTransacoes(string $paymentLinkId, ?string $dataInicio = null, ?string $dataFim = null, ?string $status = 'PAYED', int $page = 1, int $perPage = 100): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('PicPayService::listarTransacoes - Token não obtido');
            return [
                'success' => false,
                'message' => 'Erro ao autenticar com PicPay',
                'transacoes' => [],
            ];
        }

        try {
            $queryParams = [
                'page' => $page,
                'perPage' => $perPage,
                'status' => $status,
            ];

            if ($dataInicio) {
                $queryParams['startDate'] = $dataInicio;
            }

            if ($dataFim) {
                $queryParams['endDate'] = $dataFim;
            }

            $url = "{$this->apiUrl}/v1/paymentlink/{$paymentLinkId}/transactions";
            
            Log::info('PicPayService::listarTransacoes - Fazendo requisição', [
                'url' => $url,
                'payment_link_id' => $paymentLinkId,
                'query_params' => $queryParams,
                'has_token' => !empty($token),
            ]);

            $response = $this->httpClient()
                ->withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($url, $queryParams);
            
            Log::info('PicPayService::listarTransacoes - Resposta recebida', [
                'status' => $response->status(),
                'success' => $response->successful(),
                'payment_link_id' => $paymentLinkId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transacoes' => $data['transactions'] ?? [],
                    'originId' => $data['originId'] ?? $paymentLinkId,
                    'currentPage' => $data['currentPage'] ?? $page,
                    'perPage' => $data['perPage'] ?? $perPage,
                    'nextPage' => $data['nextPage'] ?? null,
                    'previousPage' => $data['previousPage'] ?? null,
                ];
            }

            Log::error('Erro ao listar transações PicPay', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payment_link_id' => $paymentLinkId,
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao listar transações',
                'transacoes' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Exceção ao listar transações PicPay', [
                'message' => $e->getMessage(),
                'payment_link_id' => $paymentLinkId,
            ]);

            return [
                'success' => false,
                'message' => 'Erro ao listar transações: ' . $e->getMessage(),
                'transacoes' => [],
            ];
        }
    }

    /**
     * Lista pagamentos realizados consultando as transações de cada payment link
     */
    public function listarPagamentosRealizados(array $referenceIds, ?string $dataInicio = null, ?string $dataFim = null): array
    {
        Log::info('=== PicPayService::listarPagamentosRealizados CHAMADO ===', [
            'reference_ids_count' => count($referenceIds),
            'reference_ids' => $referenceIds,
        ]);
        
        $token = $this->getAccessToken();

        if (!$token) {
            Log::error('PicPayService::listarPagamentosRealizados - Token não obtido');
            return [
                'success' => false,
                'message' => 'Erro ao autenticar com PicPay',
                'pagamentos' => [],
            ];
        }
        
        Log::info('PicPayService::listarPagamentosRealizados - Token obtido com sucesso');

        $pagamentosRealizados = [];
        $erros = [];

        Log::info('PicPayService::listarPagamentosRealizados - Iniciando', [
            'total_reference_ids' => count($referenceIds),
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
        ]);

        foreach ($referenceIds as $referenceId) {
            try {
                Log::info('PicPayService - Consultando payment link', [
                    'reference_id' => $referenceId,
                ]);
                
                // Primeiro, consultar o payment link para obter o payment_link_id
                $consultaPagamento = $this->consultarPagamento($referenceId);
                
                Log::info('PicPayService - Resposta consultarPagamento', [
                    'reference_id' => $referenceId,
                    'success' => $consultaPagamento['success'] ?? false,
                    'has_data' => isset($consultaPagamento['data']),
                ]);
                
                if (!$consultaPagamento['success'] || !isset($consultaPagamento['data'])) {
                    $erros[] = [
                        'reference_id' => $referenceId,
                        'erro' => $consultaPagamento['message'] ?? 'Erro ao consultar payment link',
                    ];
                    continue;
                }

                $paymentLinkData = $consultaPagamento['data'];
                
                // Log para debug da estrutura retornada
                Log::info('Estrutura payment link retornada', [
                    'reference_id' => $referenceId,
                    'data_keys' => array_keys($paymentLinkData),
                ]);
                
                // O payment_link_id pode ser o próprio reference_id ou estar em outro campo
                // A API pode retornar o ID em diferentes estruturas
                // Tentar diferentes campos possíveis
                $paymentLinkId = $paymentLinkData['id'] 
                    ?? $paymentLinkData['payment_link']['id'] 
                    ?? $paymentLinkData['originId']
                    ?? $paymentLinkData['charge']['id']
                    ?? $referenceId; // Fallback: usar o reference_id se não encontrar

                // Buscar transações pagas desse payment link
                Log::info('PicPayService - Buscando transações', [
                    'payment_link_id' => $paymentLinkId,
                    'reference_id' => $referenceId,
                ]);
                
                $resultadoTransacoes = $this->listarTransacoes($paymentLinkId, $dataInicio, $dataFim, 'PAYED', 1, 100);
                
                Log::info('PicPayService - Resposta listarTransacoes', [
                    'payment_link_id' => $paymentLinkId,
                    'success' => $resultadoTransacoes['success'] ?? false,
                    'total_transacoes' => count($resultadoTransacoes['transacoes'] ?? []),
                ]);

                if ($resultadoTransacoes['success']) {
                    foreach ($resultadoTransacoes['transacoes'] as $transacao) {
                        // Filtrar por data se fornecido
                        $dataTransacao = $transacao['createdAt'] ?? null;
                        
                        if ($dataInicio || $dataFim) {
                            if ($dataTransacao) {
                                try {
                                    $dataTransacaoObj = \Carbon\Carbon::parse($dataTransacao);
                                    
                                    if ($dataInicio && $dataTransacaoObj->lt(\Carbon\Carbon::parse($dataInicio))) {
                                        continue;
                                    }
                                    
                                    if ($dataFim && $dataTransacaoObj->gt(\Carbon\Carbon::parse($dataFim))) {
                                        continue;
                                    }
                                } catch (\Exception $e) {
                                    // Se não conseguir parsear a data, continuar
                                }
                            }
                        }

                        $pagamentosRealizados[] = [
                            'reference_id' => $referenceId,
                            'payment_link_id' => $paymentLinkId,
                            'transaction_id' => $transacao['transactionId'] ?? null,
                            'status' => strtolower($transacao['status'] ?? 'PAYED'),
                            'valor' => $transacao['amount'] ?? 0,
                            'data_pagamento' => $transacao['createdAt'] ?? null,
                            'data_atualizacao' => $transacao['updatedAt'] ?? null,
                            'dados_payment_link' => $paymentLinkData,
                            'dados_transacao' => $transacao,
                        ];
                    }
                } else {
                    $erros[] = [
                        'reference_id' => $referenceId,
                        'erro' => $resultadoTransacoes['message'] ?? 'Erro ao buscar transações',
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Erro ao consultar pagamento na listagem', [
                    'reference_id' => $referenceId,
                    'erro' => $e->getMessage(),
                ]);
                
                $erros[] = [
                    'reference_id' => $referenceId,
                    'erro' => $e->getMessage(),
                ];
            }
        }

        // Ordenar por data de pagamento (mais recente primeiro)
        usort($pagamentosRealizados, function($a, $b) {
            $dataA = $a['data_pagamento'] ?? '';
            $dataB = $b['data_pagamento'] ?? '';
            return strtotime($dataB) - strtotime($dataA);
        });

        return [
            'success' => true,
            'pagamentos' => $pagamentosRealizados,
            'total' => count($pagamentosRealizados),
            'erros' => $erros,
        ];
    }

    /**
     * Mapeia status do PicPay para status interno
     */
    public function mapearStatus(string $picpayStatus): string
    {
        return match (strtolower($picpayStatus)) {
            'created', 'pending' => 'pendente',
            'paid', 'completed' => 'confirmado',
            'expired', 'cancelled' => 'cancelado',
            'refunded' => 'cancelado',
            default => 'pendente',
        };
    }
}
