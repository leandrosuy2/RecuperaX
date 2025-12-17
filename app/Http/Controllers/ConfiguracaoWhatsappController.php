<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConfiguracaoWhatsappController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isGestor()) {
                abort(403, 'Acesso negado. Apenas administradores e gestores podem acessar as configurações.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $configuracoes = [
            'whatsapp_api_key' => config('services.whatsapp.api_key'),
            'whatsapp_api_url' => config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'),
            'whatsapp_instance_name' => config('services.whatsapp.instance_name'),
            'whatsapp_webhook_url' => config('services.whatsapp.webhook_url'),
            'whatsapp_enabled' => config('services.whatsapp.enabled', false),
        ];

        // Buscar instâncias existentes
        $instancias = [];
        $erroListagem = null;
        
        // Debug: verificar se as configurações estão sendo lidas
        if (app()->environment('local', 'development')) {
            \Illuminate\Support\Facades\Log::info('Configurações WhatsApp:', [
                'api_key_exists' => !empty($configuracoes['whatsapp_api_key']),
                'api_key_length' => strlen($configuracoes['whatsapp_api_key'] ?? ''),
                'api_url' => $configuracoes['whatsapp_api_url'],
            ]);
        }
        
        if ($configuracoes['whatsapp_api_key'] && $configuracoes['whatsapp_api_url']) {
            try {
                $instancias = $this->listarInstancias();
                
                // Debug: verificar resultado
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('Instâncias retornadas no index:', [
                        'count' => count($instancias),
                        'instancias' => $instancias
                    ]);
                }
            } catch (\Exception $e) {
                $erroListagem = $e->getMessage();
                
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::error('Erro ao listar instâncias no index:', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        } else {
            $erroListagem = 'API Key ou URL não configuradas. Configure primeiro nas configurações acima.';
        }

        return view('configuracoes.whatsapp', compact('configuracoes', 'instancias', 'erroListagem'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp_api_key' => 'required|string',
            'whatsapp_api_url' => 'required|url',
            'whatsapp_instance_name' => 'nullable|string',
            'whatsapp_webhook_url' => 'nullable|url',
            'whatsapp_enabled' => 'nullable|boolean',
        ], [
            'whatsapp_api_key.required' => 'A API Key Global é obrigatória.',
            'whatsapp_api_url.required' => 'A URL do servidor é obrigatória.',
            'whatsapp_api_url.url' => 'A URL da API deve ser uma URL válida.',
            'whatsapp_webhook_url.url' => 'A URL do Webhook deve ser uma URL válida.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Atualizar arquivo .env
        $envFile = base_path('.env');
        
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            
            // Atualizar ou adicionar variáveis
            $envContent = $this->updateEnvVariable($envContent, 'WHATSAPP_API_KEY', $request->whatsapp_api_key);
            $envContent = $this->updateEnvVariable($envContent, 'WHATSAPP_API_URL', rtrim($request->whatsapp_api_url, '/'));
            $envContent = $this->updateEnvVariable($envContent, 'WHATSAPP_INSTANCE_NAME', $request->whatsapp_instance_name ?? '');
            $envContent = $this->updateEnvVariable($envContent, 'WHATSAPP_WEBHOOK_URL', $request->whatsapp_webhook_url ?? '');
            $envContent = $this->updateEnvVariable($envContent, 'WHATSAPP_ENABLED', $request->has('whatsapp_enabled') ? 'true' : 'false');
            
            file_put_contents($envFile, $envContent);
            
            // Limpar cache de configuração
            Artisan::call('config:clear');
        }

        return redirect()->route('configuracoes.whatsapp')
            ->with('success', 'Configurações do WhatsApp atualizadas com sucesso!');
    }

    public function testarConexao(Request $request)
    {
        try {
            $apiUrl = rtrim($request->whatsapp_api_url ?? config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = $request->whatsapp_api_key ?? config('services.whatsapp.api_key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'É necessário informar a API Key Global para testar a conexão.',
                ], 400);
            }

            // Testar conectividade com Evolution API
            try {
                $testConnection = \Illuminate\Support\Facades\Http::timeout(15)
                    ->connectTimeout(10);
                
                if (app()->environment('local', 'development')) {
                    $testConnection = $testConnection->withoutVerifying();
                }

                // Evolution API usa o endpoint raiz para verificar status
                $testResponse = $testConnection->withHeaders([
                    'apikey' => $apiKey,
                ])->get($apiUrl);
                
                if ($testResponse->successful()) {
                    $data = $testResponse->json();
                    $message = 'Conexão com Evolution API estabelecida com sucesso!';
                    
                    if (isset($data['message'])) {
                        $message .= ' ' . $data['message'];
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'data' => $data,
                    ]);
                } else {
                    $errorBody = $testResponse->body();
                    $errorMessage = 'Erro na resposta da API: ' . $testResponse->status();
                    
                    // Tentar extrair mensagem de erro do JSON
                    $errorData = json_decode($errorBody, true);
                    if (isset($errorData['message'])) {
                        $errorMessage .= ' - ' . $errorData['message'];
                    } else {
                        $errorMessage .= ' - ' . substr($errorBody, 0, 200);
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                    ], 400);
                }

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $errorMsg = $e->getMessage();
                $isDns = str_contains($errorMsg, 'Resolving') || str_contains($errorMsg, 'DNS');
                $isTimeout = str_contains($errorMsg, 'timed out') || str_contains($errorMsg, 'timeout');
                
                $message = $isDns 
                    ? 'Erro de DNS: Não foi possível resolver o endereço da Evolution API. Verifique sua conexão com a internet e configurações de DNS/firewall.'
                    : ($isTimeout 
                        ? 'Timeout: A conexão com o servidor Evolution API está demorando muito. Verifique sua conexão com a internet.'
                        : 'Erro de conexão: ' . $errorMsg);
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao testar conectividade: ' . $e->getMessage(),
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar conexão: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function criarInstancia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instance_name' => 'required|string|max:255',
            'number' => 'nullable|string',
            'qrcode' => 'nullable|boolean',
            'webhook_url' => 'nullable|url',
        ], [
            'instance_name.required' => 'O nome da instância é obrigatório.',
            'webhook_url.url' => 'A URL do webhook deve ser válida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        try {
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key não configurada. Configure primeiro nas configurações.',
                ], 400);
            }

            $webhookUrl = $request->webhook_url ?? config('services.whatsapp.webhook_url') ?? url('/whatsapp/webhook');

            $payload = [
                'instanceName' => $request->instance_name,
                'qrcode' => $request->has('qrcode') ? (bool)$request->qrcode : true,
                'integration' => 'WHATSAPP-BAILEYS',
                'webhook' => [
                    'url' => $webhookUrl,
                    'webhook_by_events' => true,
                    'webhook_base64' => false,
                    'events' => [
                        'QRCODE_UPDATED',
                        'MESSAGES_UPSERT',
                        'MESSAGES_UPDATE',
                        'MESSAGES_DELETE',
                        'CONNECTION_UPDATE',
                    ],
                ],
            ];

            if ($request->filled('number')) {
                $payload['number'] = $request->number;
            }

            $httpClient = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ]);

            // Ignorar verificação SSL em ambiente local/desenvolvimento
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->post($apiUrl . '/instance/create', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Instância criada com sucesso!',
                    'data' => $data,
                ]);
            } else {
                $errorData = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $errorData['message'] ?? 'Erro ao criar instância: ' . $response->status(),
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar instância: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function obterQrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instance_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nome da instância é obrigatório.',
            ], 400);
        }

        try {
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key não configurada.',
                ], 400);
            }

            // Preparar cliente HTTP
            $httpClient = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ]);

            // Ignorar verificação SSL em ambiente local/desenvolvimento
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $instanceIdentifier = $request->instance_name;
            
            // Primeiro, buscar a instância para obter o nome correto (pode ser ID ou name)
            $instancias = $this->listarInstancias();
            $nomeCorreto = null;
            $instanciaEncontrada = null;
            
            foreach ($instancias as $inst) {
                $instId = $inst['instanceId'] ?? $inst['id'] ?? '';
                $instName = $inst['instanceName'] ?? $inst['name'] ?? '';
                
                // Verificar se o identificador passado corresponde ao ID ou nome
                if ($instId === $instanceIdentifier || $instName === $instanceIdentifier || 
                    strval($instId) === strval($instanceIdentifier) || 
                    strval($instName) === strval($instanceIdentifier)) {
                    $instanciaEncontrada = $inst;
                    // SEMPRE usar o 'name' da instância (formato correto da API v2.3+)
                    // O campo 'name' é obrigatório na Evolution API v2.3+
                    $nomeCorreto = !empty($instName) ? $instName : (!empty($instId) ? $instId : $instanceIdentifier);
                    break;
                }
            }
            
            // Se não encontrou, usar o que foi passado (pode ser que já seja o name)
            if (!$nomeCorreto) {
                $nomeCorreto = $instanceIdentifier;
            }
            
            // Log para debug
            if (app()->environment('local', 'development')) {
                \Illuminate\Support\Facades\Log::info('Buscando QR Code:', [
                    'identifier_recebido' => $instanceIdentifier,
                    'nome_correto' => $nomeCorreto,
                    'instancia_encontrada' => $instanciaEncontrada ? 'sim' : 'não',
                    'instancia_data' => $instanciaEncontrada,
                    'endpoint' => $apiUrl . '/instance/qrcode/' . urlencode($nomeCorreto)
                ]);
            }
            
            // Se a instância está em estado "connecting" ou "close", tentar desconectar primeiro para forçar novo QR code
            $statusAtual = $instanciaEncontrada['status'] ?? null;
            if ($statusAtual === 'connecting' || $statusAtual === 'close') {
                try {
                    // Tentar desconectar usando diferentes endpoints da Evolution API
                    // Endpoint 1: /instance/logout/{name}
                    $disconnectResponse = $httpClient->delete($apiUrl . '/instance/logout/' . urlencode($nomeCorreto));
                    
                    // Se não funcionar, tentar endpoint alternativo
                    if (!$disconnectResponse->successful() && $disconnectResponse->status() === 404) {
                        // Tentar com POST
                        $disconnectResponse = $httpClient->post($apiUrl . '/instance/logout/' . urlencode($nomeCorreto), []);
                    }
                    
                    if (app()->environment('local', 'development')) {
                        \Illuminate\Support\Facades\Log::info('Desconectando instância antes de gerar QR Code:', [
                            'status' => $disconnectResponse->status(),
                            'response' => $disconnectResponse->json()
                        ]);
                    }
                    
                    // Aguardar um pouco para a desconexão processar
                    usleep(500000); // 0.5 segundos
                } catch (\Exception $e) {
                    // Ignorar erro de desconexão, continuar tentando obter QR code
                    if (app()->environment('local', 'development')) {
                        \Illuminate\Support\Facades\Log::warning('Erro ao desconectar instância (continuando):', [
                            'message' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            // Tentar diferentes endpoints da Evolution API v2.3+
            // Endpoint 1: /instance/connect/{name} - este é o que funciona na v2.3+
            // Adicionar timestamp para evitar cache
            $timestamp = time();
            $response = $httpClient->get($apiUrl . '/instance/connect/' . urlencode($nomeCorreto) . '?t=' . $timestamp);
            
            // Log da resposta
            if (app()->environment('local', 'development')) {
                \Illuminate\Support\Facades\Log::info('QR Code Endpoint /connect Response:', [
                    'status' => $response->status(),
                    'has_base64' => str_contains($response->body(), 'base64')
                ]);
            }
            
            // Se não funcionar, tentar endpoint alternativo /instance/qrcode/{name}
            if (!$response->successful() && ($response->status() === 404 || $response->status() === 400)) {
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('Tentando endpoint alternativo /qrcode');
                }
                $response = $httpClient->get($apiUrl . '/instance/qrcode/' . urlencode($nomeCorreto) . '?t=' . $timestamp);
            }
            
            // Se ainda não funcionar, tentar com POST (força nova conexão)
            if (!$response->successful() && ($response->status() === 404 || $response->status() === 400)) {
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('Tentando endpoint POST /connect');
                }
                // POST força uma nova conexão e gera novo QR code
                $response = $httpClient->post($apiUrl . '/instance/connect/' . urlencode($nomeCorreto), [
                    'qrcode' => true
                ]);
            }

            if ($response->successful()) {
                $data = $response->json();
                
                // Log para debug
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('QR Code Response:', ['data' => $data]);
                }
                
                // A Evolution API v2.3+ retorna o QR code no campo 'base64' (já vem com prefixo data:image)
                $qrcode = null;
                
                // Formato 1: base64 direto (formato da Evolution API v2.3+)
                if (isset($data['base64'])) {
                    $qrcode = $data['base64'];
                }
                // Formato 2: qrcode.code ou qrcode.base64
                elseif (isset($data['qrcode'])) {
                    if (is_string($data['qrcode'])) {
                        $qrcode = $data['qrcode'];
                    } elseif (is_array($data['qrcode'])) {
                        $qrcode = $data['qrcode']['base64'] ?? $data['qrcode']['code'] ?? $data['qrcode']['qrcode'] ?? null;
                    }
                }
                // Formato 3: code direto (pode ser usado para gerar QR code)
                elseif (isset($data['code'])) {
                    // Se for código de texto, converter para QR code via API externa
                    $qrcode = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($data['code']);
                }
                // Formato 4: qrCode (camelCase)
                elseif (isset($data['qrCode'])) {
                    $qrcode = is_string($data['qrCode']) ? $data['qrCode'] : ($data['qrCode']['base64'] ?? $data['qrCode']['code'] ?? null);
                }

                if ($qrcode) {
                    return response()->json([
                        'success' => true,
                        'message' => 'QR Code obtido com sucesso!',
                        'qrcode' => $qrcode,
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code não encontrado na resposta da API. A instância pode já estar conectada ou o formato da resposta mudou.',
                        'data' => $data,
                    ], 400);
                }
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Erro ao obter QR Code';
                
                // Melhorar mensagens de erro comuns
                if (str_contains(strtolower($errorMessage), 'not found') || $response->status() === 404) {
                    $errorMessage = 'Instância não encontrada. Verifique se o nome da instância está correto.';
                } elseif (str_contains(strtolower($errorMessage), 'connect') || str_contains(strtolower($errorMessage), 'dispositivo')) {
                    $errorMessage = 'Não foi possível conectar ao dispositivo. A instância pode estar em uso ou desconectada.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage . ' (Status: ' . $response->status() . ')',
                    'error_data' => $errorData,
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter QR Code: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function listarInstancias()
    {
        try {
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            if (!$apiKey) {
                return [];
            }

            $httpClient = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders([
                    'apikey' => $apiKey,
                ]);

            // Ignorar verificação SSL em ambiente local/desenvolvimento
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->get($apiUrl . '/instance/fetchInstances');

            if ($response->successful()) {
                $data = $response->json();
                
                // Debug: log da resposta (apenas em desenvolvimento)
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('Evolution API Response (listarInstancias):', [
                        'url' => $apiUrl . '/instance/fetchInstances',
                        'data_type' => gettype($data),
                        'is_array' => is_array($data),
                        'count' => is_array($data) ? count($data) : 0,
                        'first_item_keys' => is_array($data) && isset($data[0]) && is_array($data[0]) ? array_keys($data[0]) : []
                    ]);
                }
                
                // Se não for array, retornar vazio
                if (!is_array($data)) {
                    if (app()->environment('local', 'development')) {
                        \Illuminate\Support\Facades\Log::warning('Resposta não é array:', ['type' => gettype($data)]);
                    }
                    return [];
                }
                
                // Se o array estiver vazio
                if (empty($data)) {
                    if (app()->environment('local', 'development')) {
                        \Illuminate\Support\Facades\Log::info('Array de resposta está vazio');
                    }
                    return [];
                }
                
                $instancias = [];
                
                // Verificar se é um array de objetos com 'instance'
                foreach ($data as $key => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    
                    // Formato Evolution API v2.3+: Array direto de instâncias
                    // Cada item já é uma instância com campos: id, name, connectionStatus, etc.
                    if (isset($item['name']) || isset($item['id']) || isset($item['connectionStatus'])) {
                        // Normalizar o formato para compatibilidade
                        $instanciaNormalizada = [
                            'instanceName' => $item['name'] ?? 'Sem nome',
                            'status' => $item['connectionStatus'] ?? 'desconhecido',
                            'instanceId' => $item['id'] ?? null,
                            'profileName' => $item['profileName'] ?? null,
                            'profilePicUrl' => $item['profilePicUrl'] ?? null,
                            'number' => $item['number'] ?? null,
                            'integration' => $item['integration'] ?? null,
                            'token' => $item['token'] ?? null,
                            'createdAt' => $item['createdAt'] ?? null,
                            'updatedAt' => $item['updatedAt'] ?? null,
                            // Manter dados originais também
                            '_raw' => $item,
                        ];
                        $instancias[] = $instanciaNormalizada;
                    }
                    // Formato 1: Array de objetos com propriedade 'instance'
                    elseif (isset($item['instance']) && is_array($item['instance'])) {
                        $instancias[] = $item['instance'];
                    }
                    // Formato alternativo: já é uma instância
                    elseif (isset($item['instanceName']) || isset($item['status']) || isset($item['instanceId'])) {
                        $instancias[] = $item;
                    }
                }
                
                // Debug: log das instâncias processadas
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('Instâncias processadas (listarInstancias):', [
                        'count' => count($instancias),
                        'instancias' => $instancias
                    ]);
                }
                
                return $instancias;
            } else {
                // Log do erro
                $errorBody = $response->body();
                $errorData = json_decode($errorBody, true);
                
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::error('Evolution API Error:', [
                        'status' => $response->status(),
                        'body' => $errorBody,
                        'error_data' => $errorData
                    ]);
                }
                
                // Lançar exceção para ser capturada no index()
                throw new \Exception('Erro ao buscar instâncias: ' . ($errorData['message'] ?? $response->status()));
            }

            return [];

        } catch (\Exception $e) {
            // Log do erro (opcional)
            return [];
        }
    }

    public function statusInstancia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instance_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nome da instância é obrigatório.',
            ], 400);
        }

        try {
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            $httpClient = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders([
                    'apikey' => $apiKey,
                ]);

            // Ignorar verificação SSL em ambiente local/desenvolvimento
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->get($apiUrl . '/instance/fetchInstances');

            if ($response->successful()) {
                $data = $response->json();
                
                // A Evolution API retorna um array de objetos, cada um com uma propriedade 'instance'
                $instancias = [];
                if (is_array($data)) {
                    foreach ($data as $item) {
                        if (isset($item['instance'])) {
                            $instancias[] = $item['instance'];
                        } elseif (isset($item['instanceName'])) {
                            $instancias[] = $item;
                        }
                    }
                } else {
                    $instancias = $data['instance'] ?? [];
                }
                
                foreach ($instancias as $instancia) {
                    $instName = $instancia['instanceName'] ?? $instancia['name'] ?? '';
                    $instId = $instancia['instanceId'] ?? $instancia['id'] ?? '';
                    
                    if ($instName === $request->instance_name || $instId === $request->instance_name) {
                        return response()->json([
                            'success' => true,
                            'data' => $instancia,
                        ]);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Instância não encontrada.',
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar status da instância.',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deletarInstancia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instance_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nome da instância é obrigatório.',
            ], 400);
        }

        try {
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            $httpClient = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders([
                    'apikey' => $apiKey,
                ]);

            // Ignorar verificação SSL em ambiente local/desenvolvimento
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            // Tentar deletar usando o nome ou ID
            $instanceIdentifier = urlencode($request->instance_name);
            $response = $httpClient->delete($apiUrl . '/instance/delete/' . $instanceIdentifier);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Instância deletada com sucesso!',
                ]);
            } else {
                $errorData = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $errorData['message'] ?? 'Erro ao deletar instância.',
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar instância: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function enviarMensagemTeste(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instance_name' => 'required|string',
            'numero' => 'required|string',
            'mensagem' => 'required|string|max:4096',
        ], [
            'instance_name.required' => 'Nome da instância é obrigatório.',
            'numero.required' => 'Número do WhatsApp é obrigatório.',
            'mensagem.required' => 'A mensagem é obrigatória.',
            'mensagem.max' => 'A mensagem não pode ter mais de 4096 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        try {
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key não configurada.',
                ], 400);
            }

            // Preparar cliente HTTP com timeout menor para evitar travamento
            $httpClient = \Illuminate\Support\Facades\Http::timeout(20)
                ->connectTimeout(10)
                ->withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ]);

            // Ignorar verificação SSL em ambiente local/desenvolvimento
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $instanceIdentifier = $request->instance_name;
            
            // Buscar a instância para obter o nome correto e verificar status
            $instancias = $this->listarInstancias();
            $nomeCorreto = null;
            $statusInstancia = null;
            $instanciaEncontrada = null;
            
            foreach ($instancias as $inst) {
                $instId = $inst['instanceId'] ?? $inst['id'] ?? '';
                $instName = $inst['instanceName'] ?? $inst['name'] ?? '';
                
                // Comparação mais flexível (case-insensitive e trim)
                $instNameTrimmed = trim($instName);
                $instIdTrimmed = trim($instId);
                $identifierTrimmed = trim($instanceIdentifier);
                
                if ($instIdTrimmed === $identifierTrimmed || 
                    $instNameTrimmed === $identifierTrimmed ||
                    strcasecmp($instIdTrimmed, $identifierTrimmed) === 0 ||
                    strcasecmp($instNameTrimmed, $identifierTrimmed) === 0) {
                    $instanciaEncontrada = $inst;
                    // SEMPRE usar o nome exato da instância encontrada (não o ID)
                    $nomeCorreto = !empty($instNameTrimmed) ? $instNameTrimmed : (!empty($instIdTrimmed) ? $instIdTrimmed : $identifierTrimmed);
                    $statusInstancia = strtolower($inst['status'] ?? $inst['connectionStatus'] ?? 'unknown');
                    
                    if (app()->environment('local', 'development')) {
                        \Illuminate\Support\Facades\Log::info('Instância encontrada para envio de mensagem:', [
                            'identifier_recebido' => $instanceIdentifier,
                            'nome_encontrado' => $nomeCorreto,
                            'id_encontrado' => $instIdTrimmed,
                            'status' => $statusInstancia
                        ]);
                    }
                    break;
                }
            }
            
            if (!$nomeCorreto) {
                $nomeCorreto = trim($instanceIdentifier);
                
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::warning('Instância não encontrada, usando identifier recebido:', [
                        'identifier' => $instanceIdentifier,
                        'instancias_disponiveis' => array_map(function($inst) {
                            return [
                                'name' => $inst['instanceName'] ?? $inst['name'] ?? 'N/A',
                                'id' => $inst['instanceId'] ?? $inst['id'] ?? 'N/A'
                            ];
                        }, $instancias)
                    ]);
                }
            }

            // Verificar se a instância está conectada antes de enviar
            if ($statusInstancia !== 'open' && $instanciaEncontrada) {
                $statusFormatado = ucfirst($statusInstancia);
                return response()->json([
                    'success' => false,
                    'message' => "A instância não está conectada. Status atual: {$statusFormatado}. Conecte a instância primeiro antes de enviar mensagens.",
                ], 400);
            }

            // Limpar e formatar número (remover caracteres não numéricos, exceto +)
            $numero = preg_replace('/[^0-9+]/', '', $request->numero);
            
            // Se não começar com +, adicionar se necessário (assumindo Brasil se começar com 55)
            if (!str_starts_with($numero, '+')) {
                // Se já começar com código do país, manter; senão, assumir que precisa adicionar
                if (!str_starts_with($numero, '55')) {
                    // Se não tem código do país, adicionar 55 (Brasil)
                    $numero = '55' . $numero;
                }
            } else {
                // Remover o + para o formato E164
                $numero = ltrim($numero, '+');
            }

            // Validar formato do número (deve ter pelo menos 10 dígitos após código do país)
            $numeroLimpo = preg_replace('/\D/', '', $numero);
            if (strlen($numeroLimpo) < 12 || strlen($numeroLimpo) > 15) {
                return response()->json([
                    'success' => false,
                    'message' => 'Número de telefone inválido. O número deve ter entre 12 e 15 dígitos (incluindo código do país). Exemplo: 5511999999999',
                ], 400);
            }

            // Aguardar um pouco para garantir que a conexão está estável
            usleep(500000); // 0.5 segundos

            // Payload para Evolution API - usar apenas número sem @s.whatsapp.net
            $payload = [
                'number' => $numero,
                'text' => $request->mensagem,
            ];

            // Log para debug
            if (app()->environment('local', 'development')) {
                \Illuminate\Support\Facades\Log::info('Enviando mensagem de teste:', [
                    'instance_name' => $nomeCorreto,
                    'instance_name_original' => $request->instance_name,
                    'numero' => $numero,
                    'endpoint' => $apiUrl . '/message/sendText/' . rawurlencode($nomeCorreto),
                    'endpoint_decoded' => $apiUrl . '/message/sendText/' . $nomeCorreto,
                    'instancia_encontrada' => $instanciaEncontrada ? 'sim' : 'não'
                ]);
            }

            // Enviar mensagem via Evolution API com timeout menor para evitar travamento
            // Usar rawurlencode para converter espaços em %20 (formato correto para Evolution API)
            $endpointUrl = $apiUrl . '/message/sendText/' . rawurlencode($nomeCorreto);
            
            try {
                $response = $httpClient->timeout(15)->post($endpointUrl, $payload);
                
                // Se retornar 404, tentar com o ID da instância como fallback
                if ($response->status() === 404 && $instanciaEncontrada) {
                    $instanceId = $instanciaEncontrada['instanceId'] ?? $instanciaEncontrada['id'] ?? null;
                    if ($instanceId && $instanceId !== $nomeCorreto) {
                        if (app()->environment('local', 'development')) {
                            \Illuminate\Support\Facades\Log::info('Tentando enviar mensagem com ID da instância como fallback:', [
                                'nome_falhou' => $nomeCorreto,
                                'id_tentando' => $instanceId
                            ]);
                        }
                        $endpointUrl = $apiUrl . '/message/sendText/' . rawurlencode($instanceId);
                        $response = $httpClient->timeout(15)->post($endpointUrl, $payload);
                    }
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Se houver erro de conexão, não causar desconexão da instância
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de conexão ao enviar mensagem. Verifique se o número está correto e se a instância está conectada.',
                ], 400);
            } catch (\Exception $e) {
                // Capturar outros erros sem causar desconexão
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::error('Exceção ao enviar mensagem:', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao enviar mensagem: ' . $e->getMessage(),
                ], 500);
            }

            if ($response->successful()) {
                $data = $response->json();
                
                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::info('Mensagem enviada com sucesso:', ['data' => $data]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Mensagem enviada com sucesso!',
                    'data' => $data,
                ]);
            } else {
                $errorData = $response->json();
                $errorBody = $response->body();
                $errorMessage = null;
                
                // Tentar extrair mensagem de erro de diferentes formatos
                if (is_array($errorData)) {
                    $errorMessage = $errorData['message'] ?? $errorData['error'] ?? null;
                    
                    // Verificar se há erro aninhado
                    if (!$errorMessage && isset($errorData['error'])) {
                        if (is_array($errorData['error'])) {
                            $errorMessage = $errorData['error']['message'] ?? $errorData['error']['error'] ?? null;
                        } elseif (is_string($errorData['error'])) {
                            $errorMessage = $errorData['error'];
                        }
                    }
                    
                    // Verificar se há response com erro
                    if (!$errorMessage && isset($errorData['response']['message'])) {
                        $errorMessage = $errorData['response']['message'];
                    }
                }
                
                // Se não encontrou mensagem, usar padrão
                if (!$errorMessage) {
                    $errorMessage = 'Erro ao enviar mensagem';
                }
                
                // Verificar primeiro se o erro é sobre número não existir no WhatsApp
                if (isset($errorData['response']['message']) && is_array($errorData['response']['message'])) {
                    foreach ($errorData['response']['message'] as $msg) {
                        if (is_array($msg) && isset($msg['exists']) && $msg['exists'] === false) {
                            $numeroErro = $msg['number'] ?? $numero;
                            $errorMessage = "O número {$numeroErro} não existe no WhatsApp ou não está registrado. Verifique se o número está correto e se o contato possui WhatsApp ativo.";
                            break;
                        }
                    }
                }
                
                // Melhorar mensagens de erro comuns
                $errorLower = strtolower($errorMessage ?? '');
                if (str_contains($errorLower, 'not found') || $response->status() === 404) {
                    if (!str_contains($errorLower, 'não existe no whatsapp')) {
                        $errorMessage = 'Instância não encontrada. Verifique se o nome da instância está correto.';
                    }
                } elseif (str_contains($errorLower, 'connection closed') || str_contains($errorLower, 'disconnected') || str_contains($errorLower, 'close')) {
                    $errorMessage = 'A instância não está conectada. Status: desconectada. Conecte a instância primeiro antes de enviar mensagens.';
                } elseif (str_contains($errorLower, 'connect') || str_contains($errorLower, 'connecting')) {
                    $errorMessage = 'A instância está em processo de conexão. Aguarde a conexão ser estabelecida antes de enviar mensagens.';
                } elseif (str_contains($errorLower, 'number') || str_contains($errorLower, 'invalid')) {
                    if (!str_contains($errorLower, 'não existe no whatsapp')) {
                        $errorMessage = 'Número de telefone inválido ou não encontrado no WhatsApp. Verifique se o número está correto e se o contato existe no WhatsApp (ex: 5511999999999).';
                    }
                } elseif ($response->status() === 500) {
                    $errorMessage = 'Erro interno do servidor. O número pode estar incorreto ou o WhatsApp pode ter desconectado. Verifique o número e tente novamente.';
                } elseif (str_contains($errorLower, 'device_removed') || str_contains($errorLower, 'conflict')) {
                    $errorMessage = 'Erro de autenticação. O WhatsApp pode ter sido desconectado em outro dispositivo. Reconecte a instância.';
                } elseif ($response->status() === 400 && !str_contains($errorLower, 'não existe no whatsapp')) {
                    $errorMessage = 'Erro na requisição. Verifique se o número está correto e se o contato existe no WhatsApp.';
                }

                if (app()->environment('local', 'development')) {
                    \Illuminate\Support\Facades\Log::error('Erro ao enviar mensagem:', [
                        'status' => $response->status(),
                        'error_data' => $errorData,
                        'error_body' => $errorBody,
                        'error_message' => $errorMessage
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage . ' (Status: ' . $response->status() . ')',
                    'error_data' => $errorData,
                ], 400);
            }

        } catch (\Exception $e) {
            if (app()->environment('local', 'development')) {
                \Illuminate\Support\Facades\Log::error('Exceção ao enviar mensagem:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualiza ou adiciona uma variável no arquivo .env
     */
    private function updateEnvVariable($content, $key, $value)
    {
        // Escapar caracteres especiais no valor
        $escapedValue = preg_replace('/\s+/', ' ', trim($value));
        if (preg_match('/[#\s"\'=]/', $escapedValue)) {
            $escapedValue = '"' . addslashes($escapedValue) . '"';
        }

        // Verificar se a variável já existe
        $pattern = "/^{$key}=.*/m";
        
        if (preg_match($pattern, $content)) {
            // Atualizar variável existente
            return preg_replace($pattern, "{$key}={$escapedValue}", $content);
        } else {
            // Adicionar nova variável no final do arquivo
            return rtrim($content) . "\n{$key}={$escapedValue}\n";
        }
    }
}
