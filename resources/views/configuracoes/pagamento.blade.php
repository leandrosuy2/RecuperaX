<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Configurações de Pagamento</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure as integrações de pagamento do sistema</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Configurações PicPay -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">PicPay</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure as credenciais da API do PicPay</p>
                </div>
                <button onclick="testarConexao()" 
                        id="btn-testar"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Testar Conexão
                </button>
            </div>

            <form action="{{ route('configuracoes.pagamento.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="picpay_client_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Client ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="picpay_client_id"
                               name="picpay_client_id" 
                               value="{{ old('picpay_client_id', $configuracoes['picpay_client_id']) }}"
                               required
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('picpay_client_id') border-red-300 dark:border-red-600 @enderror">
                        @error('picpay_client_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ID do cliente fornecido pelo PicPay</p>
                    </div>

                    <div>
                        <label for="picpay_client_secret" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Client Secret <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="picpay_client_secret"
                                   name="picpay_client_secret" 
                                   value="{{ old('picpay_client_secret', $configuracoes['picpay_client_secret']) }}"
                                   required
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('picpay_client_secret') border-red-300 dark:border-red-600 @enderror">
                            <button type="button" 
                                    onclick="togglePassword('picpay_client_secret')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <svg id="eye-picpay_client_secret" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-off-picpay_client_secret" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('picpay_client_secret')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Secret do cliente fornecido pelo PicPay</p>
                    </div>

                    <div>
                        <label for="picpay_api_url" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            URL da API
                        </label>
                        <input type="url" 
                               id="picpay_api_url"
                               name="picpay_api_url" 
                               value="{{ old('picpay_api_url', $configuracoes['picpay_api_url']) }}"
                               placeholder="https://api.picpay.com"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('picpay_api_url') border-red-300 dark:border-red-600 @enderror">
                        @error('picpay_api_url')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL base da API do PicPay (padrão: https://api.picpay.com)</p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="picpay_sandbox"
                               name="picpay_sandbox" 
                               value="1"
                               {{ old('picpay_sandbox', $configuracoes['picpay_sandbox']) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-indigo-500">
                        <label for="picpay_sandbox" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Modo Sandbox (Ambiente de testes)
                        </label>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-blue-900 dark:text-blue-100 mb-2">Informações sobre Webhook</h4>
                        <p class="text-xs text-blue-800 dark:text-blue-200 mb-2">
                            Configure o webhook no painel do PicPay para receber notificações de pagamento:
                        </p>
                        <div class="bg-white dark:bg-gray-800 rounded border border-blue-200 dark:border-blue-700 p-2">
                            <code class="text-xs text-gray-800 dark:text-gray-200 break-all">
                                {{ url('/picpay/webhook') }}
                            </code>
                        </div>
                        <button type="button" 
                                onclick="copiarWebhook()"
                                class="mt-2 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline">
                            Copiar URL do Webhook
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        Salvar Configurações
                    </button>
                    <a href="{{ route('dashboard') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Documentação -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Documentação</h3>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p>Para obter suas credenciais do PicPay:</p>
                <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Acesse o painel do desenvolvedor do PicPay</li>
                    <li>Navegue até a seção de credenciais da API</li>
                    <li>Copie o Client ID e Client Secret</li>
                    <li>Cole as credenciais nos campos acima</li>
                </ol>
                <p class="mt-4">
                    <a href="https://developers-business.picpay.com" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline">
                        Acessar documentação oficial do PicPay
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById('eye-' + fieldId);
            const eyeOff = document.getElementById('eye-off-' + fieldId);
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.add('hidden');
                eyeOff.classList.remove('hidden');
            } else {
                field.type = 'password';
                eye.classList.remove('hidden');
                eyeOff.classList.add('hidden');
            }
        }

        function copiarWebhook() {
            const url = '{{ url("/picpay/webhook") }}';
            navigator.clipboard.writeText(url).then(() => {
                alert('URL do webhook copiada para a área de transferência!');
            }).catch(() => {
                // Fallback para navegadores mais antigos
                const textarea = document.createElement('textarea');
                textarea.value = url;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('URL do webhook copiada para a área de transferência!');
            });
        }

        function testarConexao() {
            const button = document.getElementById('btn-testar');
            const originalText = button.textContent;
            button.disabled = true;
            button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testando...';

            fetch('{{ route("configuracoes.pagamento.testar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    picpay_client_id: document.getElementById('picpay_client_id').value,
                    picpay_client_secret: document.getElementById('picpay_client_secret').value,
                    picpay_api_url: document.getElementById('picpay_api_url').value,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Exibir modal com os dados do pagamento de teste
                    mostrarModalTeste(data);
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('✗ Erro ao testar conexão. Verifique o console para mais detalhes.');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Testar Conexão';
            });
        }

        function mostrarModalTeste(data) {
            // Criar ou atualizar modal de teste
            let modal = document.getElementById('modalTestePicPay');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'modalTestePicPay';
                modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center hidden';
                document.body.appendChild(modal);
            }

            // Gerar QR Code do link ou brcode se não tiver base64
            const qrcodeHtml = data.qrcode_base64 
                ? `<div class="text-center mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">QR Code do Pagamento de Teste</h4>
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                        <img src="data:image/png;base64,${data.qrcode_base64}" alt="QR Code PicPay" class="w-64 h-64 mx-auto">
                    </div>
                </div>`
                : (data.payment_url || data.brcode
                    ? `<div class="text-center mb-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">QR Code do Pagamento de Teste</h4>
                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data.brcode || data.payment_url)}" alt="QR Code PicPay" class="w-64 h-64 mx-auto">
                        </div>
                    </div>`
                    : '');

            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] flex flex-col">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">✓ Conexão Estabelecida!</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pagamento de teste criado com sucesso</p>
                            </div>
                            <button onclick="fecharModalTeste()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-6 overflow-y-auto flex-1">
                        <div class="space-y-6">
                            ${qrcodeHtml}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Link de Pagamento de Teste</label>
                                <div class="flex gap-2">
                                    <input type="text" id="test-payment-url" value="${data.payment_url || ''}" readonly class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-900">
                                    <button onclick="copiarLinkTeste()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Copiar</button>
                                </div>
                            </div>
                            <div>
                                <a href="${data.payment_url || '#'}" target="_blank" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Abrir Link de Pagamento
                                </a>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <p class="text-sm text-green-800 dark:text-green-200">
                                    <strong>✓ Sucesso!</strong> A conexão com a API do PicPay está funcionando corretamente. Este é um pagamento de teste que pode ser cancelado.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                        <button onclick="fecharModalTeste()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
                            Fechar
                        </button>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Fechar ao clicar fora
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    fecharModalTeste();
                }
            });
        }

        function fecharModalTeste() {
            const modal = document.getElementById('modalTestePicPay');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function copiarLinkTeste() {
            const input = document.getElementById('test-payment-url');
            input.select();
            input.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(input.value).then(() => {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copiado!';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-indigo-600');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-indigo-600');
                }, 2000);
            }).catch(() => {
                document.execCommand('copy');
                alert('Link copiado!');
            });
        }
    </script>
</x-app-layout>
