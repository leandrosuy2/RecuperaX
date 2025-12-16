<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConfiguracaoPagamentoController extends Controller
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
            'picpay_client_id' => config('services.picpay.client_id'),
            'picpay_client_secret' => config('services.picpay.client_secret'),
            'picpay_api_url' => config('services.picpay.api_url', 'https://api.picpay.com'),
            'picpay_sandbox' => config('services.picpay.sandbox', false),
        ];

        return view('configuracoes.pagamento', compact('configuracoes'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'picpay_client_id' => 'required|string',
            'picpay_client_secret' => 'required|string',
            'picpay_api_url' => 'nullable|url',
            'picpay_sandbox' => 'nullable|boolean',
        ], [
            'picpay_client_id.required' => 'O Client ID do PicPay é obrigatório.',
            'picpay_client_secret.required' => 'O Client Secret do PicPay é obrigatório.',
            'picpay_api_url.url' => 'A URL da API deve ser uma URL válida.',
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
            $envContent = $this->updateEnvVariable($envContent, 'PICPAY_CLIENT_ID', $request->picpay_client_id);
            $envContent = $this->updateEnvVariable($envContent, 'PICPAY_CLIENT_SECRET', $request->picpay_client_secret);
            $envContent = $this->updateEnvVariable($envContent, 'PICPAY_API_URL', $request->picpay_api_url ?? 'https://api.picpay.com');
            $envContent = $this->updateEnvVariable($envContent, 'PICPAY_SANDBOX', $request->has('picpay_sandbox') ? 'true' : 'false');
            
            file_put_contents($envFile, $envContent);
            
            // Limpar cache de configuração
            Artisan::call('config:clear');
        }

        return redirect()->route('configuracoes.pagamento')
            ->with('success', 'Configurações de pagamento atualizadas com sucesso!');
    }

    public function testarConexao(Request $request)
    {
        try {
            // Temporariamente atualizar configuração se fornecida
            if ($request->has('picpay_client_id') && $request->has('picpay_client_secret')) {
                config([
                    'services.picpay.client_id' => $request->picpay_client_id,
                    'services.picpay.client_secret' => $request->picpay_client_secret,
                ]);
                
                if ($request->has('picpay_api_url')) {
                    config(['services.picpay.api_url' => $request->picpay_api_url]);
                }
            }

            $apiUrl = $request->picpay_api_url ?? config('services.picpay.api_url', 'https://api.picpay.com');
            
            // Primeiro, testar se consegue resolver o DNS e conectar
            try {
                $testConnection = \Illuminate\Support\Facades\Http::timeout(10)
                    ->connectTimeout(5);
                
                if (app()->environment('local', 'development')) {
                    $testConnection = $testConnection->withoutVerifying();
                }
                
                $testResponse = $testConnection->get($apiUrl);
                
                // Se chegou aqui, a conexão básica funciona
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $errorMsg = $e->getMessage();
                $isDns = str_contains($errorMsg, 'Resolving') || str_contains($errorMsg, 'DNS');
                $isTimeout = str_contains($errorMsg, 'timed out') || str_contains($errorMsg, 'timeout');
                
                $message = $isDns 
                    ? 'Erro de DNS: Não foi possível resolver o endereço da API PicPay. Verifique sua conexão com a internet e configurações de DNS/firewall.'
                    : ($isTimeout 
                        ? 'Timeout: A conexão com o servidor PicPay está demorando muito. Verifique sua conexão com a internet.'
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

            $picpayService = new \App\Services\PicPayService();
            
            // Tentar criar um pagamento de teste (valor mínimo para cartão é maior)
            // Usar apenas BRCODE para teste, pois cartão tem valor mínimo maior
            $dadosTeste = [
                'reference_id' => 'TEST-' . time(),
                'valor' => 1.00, // Valor mínimo de R$ 1,00 para teste
                'callback_url' => route('picpay.webhook'),
                'return_url' => url('/'),
                'expires_at' => now()->addDays(30)->format('Y-m-d'),
                'charge_name' => 'Teste de Conexão PicPay',
                'charge_description' => 'Teste de integração com API PicPay',
                'payment_methods' => ['BRCODE'], // Apenas BRCODE para teste (evita erro de valor mínimo do cartão)
                'brcode_arrangements' => ['PICPAY', 'PIX'],
                'allow_create_pix_key' => true,
                // Não incluir card_max_installment_number quando não há CREDIT_CARD
            ];

            $resultado = $picpayService->criarPagamento($dadosTeste);

            if ($resultado['success']) {
                // Retornar dados completos para exibir no modal
                // Não cancelar o pagamento de teste - deixar o usuário ver o QR Code e link
                return response()->json([
                    'success' => true,
                    'message' => 'Conexão com PicPay estabelecida com sucesso!',
                    'payment_url' => $resultado['payment_url'],
                    'qrcode_base64' => $resultado['qrcode_base64'],
                    'data' => $resultado['data'],
                ]);
            }

            $errorMessage = $resultado['message'] ?? 'Não foi possível estabelecer conexão.';
            
            // Melhorar mensagens de erro comuns
            if (str_contains($errorMessage, 'autenticar') || str_contains($errorMessage, 'token')) {
                $errorMessage = 'Erro de autenticação: Verifique se o Client ID e Client Secret estão corretos.';
            } elseif (str_contains($errorMessage, 'timeout') || str_contains($errorMessage, 'timed out')) {
                $errorMessage = 'Timeout: A conexão está demorando muito. Verifique sua conexão com a internet.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar conexão: ' . $e->getMessage(),
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
