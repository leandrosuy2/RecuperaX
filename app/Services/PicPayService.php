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
            $payload = [
                'referenceId' => $dados['reference_id'],
                'callbackUrl' => $dados['callback_url'],
                'returnUrl' => $dados['return_url'] ?? null,
                'value' => number_format($dados['valor'], 2, '.', ''),
                'expiresAt' => $dados['expires_at'] ?? now()->addHours(24)->toIso8601String(),
                'buyer' => [
                    'firstName' => $dados['buyer']['first_name'],
                    'lastName' => $dados['buyer']['last_name'] ?? '',
                    'document' => preg_replace('/[^0-9]/', '', $dados['buyer']['document']),
                    'email' => $dados['buyer']['email'],
                    'phone' => preg_replace('/[^0-9]/', '', $dados['buyer']['phone'] ?? ''),
                ],
            ];

            $response = $this->httpClient()
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/v1/payments", $payload);

            if ($response->successful()) {
                $data = $response->json();
                // A API de Link de Pagamento retorna qrcode como objeto com content e base64
                $qrcodeData = $data['qrcode'] ?? null;
                $qrcodeBase64 = null;
                if (is_array($qrcodeData) && isset($qrcodeData['base64'])) {
                    $qrcodeBase64 = $qrcodeData['base64'];
                } elseif (isset($data['qrcodeBase64'])) {
                    $qrcodeBase64 = $data['qrcodeBase64'];
                }
                
                return [
                    'success' => true,
                    'data' => $data,
                    'payment_url' => $data['paymentUrl'] ?? null,
                    'qrcode' => is_array($qrcodeData) ? ($qrcodeData['content'] ?? null) : $qrcodeData,
                    'qrcode_base64' => $qrcodeBase64,
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
                ->get("{$this->apiUrl}/v1/payments/{$referenceId}/status");

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
                ->post("{$this->apiUrl}/v1/payments/{$referenceId}/cancellations", $payload);

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
