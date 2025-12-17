<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Configurações do WhatsApp</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure a integração da Evolution API para WhatsApp</p>
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

        <!-- Configurações WhatsApp -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Evolution API</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Configure as credenciais da Evolution API para WhatsApp</p>
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

            <form action="{{ route('configuracoes.whatsapp.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="whatsapp_enabled"
                               name="whatsapp_enabled" 
                               value="1"
                               {{ old('whatsapp_enabled', $configuracoes['whatsapp_enabled']) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-indigo-500">
                        <label for="whatsapp_enabled" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Habilitar integração do WhatsApp
                        </label>
                    </div>

                    <div>
                        <label for="whatsapp_api_url" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Server URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url" 
                               id="whatsapp_api_url"
                               name="whatsapp_api_url" 
                               value="{{ old('whatsapp_api_url', $configuracoes['whatsapp_api_url']) }}"
                               placeholder="https://recuperax-evolution-api.npfp58.easypanel.host"
                               required
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp_api_url') border-red-300 dark:border-red-600 @enderror">
                        @error('whatsapp_api_url')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL do servidor Evolution API</p>
                    </div>

                    <div>
                        <label for="whatsapp_api_key" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            API Key Global <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="whatsapp_api_key"
                                   name="whatsapp_api_key" 
                                   value="{{ old('whatsapp_api_key', $configuracoes['whatsapp_api_key']) }}"
                                   required
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp_api_key') border-red-300 dark:border-red-600 @enderror">
                            <button type="button" 
                                    onclick="togglePassword('whatsapp_api_key')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <svg id="eye-whatsapp_api_key" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-off-whatsapp_api_key" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('whatsapp_api_key')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Chave de API Global da Evolution API</p>
                    </div>

                    <div>
                        <label for="whatsapp_instance_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nome da Instância (Opcional)
                        </label>
                        <input type="text" 
                               id="whatsapp_instance_name"
                               name="whatsapp_instance_name" 
                               value="{{ old('whatsapp_instance_name', $configuracoes['whatsapp_instance_name']) }}"
                               placeholder="Ex: instancia-principal"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp_instance_name') border-red-300 dark:border-red-600 @enderror">
                        @error('whatsapp_instance_name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nome da instância do WhatsApp (opcional, usado para identificar múltiplas instâncias)</p>
                    </div>

                    <div>
                        <label for="whatsapp_webhook_url" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            URL do Webhook
                        </label>
                        <input type="url" 
                               id="whatsapp_webhook_url"
                               name="whatsapp_webhook_url" 
                               value="{{ old('whatsapp_webhook_url', $configuracoes['whatsapp_webhook_url']) }}"
                               placeholder="https://seudominio.com.br/whatsapp/webhook"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp_webhook_url') border-red-300 dark:border-red-600 @enderror">
                        @error('whatsapp_webhook_url')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL para receber notificações do WhatsApp</p>
                    </div>


                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-blue-900 dark:text-blue-100 mb-2">Informações sobre Webhook</h4>
                        <p class="text-xs text-blue-800 dark:text-blue-200 mb-2">
                            Configure o webhook no painel do WhatsApp Business para receber notificações:
                        </p>
                        <div class="bg-white dark:bg-gray-800 rounded border border-blue-200 dark:border-blue-700 p-2">
                            <code class="text-xs text-gray-800 dark:text-gray-200 break-all">
                                {{ url('/whatsapp/webhook') }}
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

        <!-- Gerenciamento de Instâncias -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gerenciar Instâncias</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Crie e gerencie suas instâncias do WhatsApp</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="atualizarListaInstancias()" 
                            class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Atualizar
                    </button>
                    <button onclick="abrirModalCriarInstancia()" 
                            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar Nova Instância
                    </button>
                </div>
            </div>

            <!-- Lista de Instâncias -->
            <div id="lista-instancias" class="space-y-3">
                @if(isset($erroListagem))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-sm text-red-800 dark:text-red-200">
                        <strong>Erro ao listar instâncias:</strong> {{ $erroListagem }}
                    </p>
                </div>
                @endif
                
                @if(isset($instancias) && is_array($instancias) && count($instancias) > 0)
                    @foreach($instancias as $instancia)
                    @php
                        $instanceName = $instancia['instanceName'] ?? $instancia['name'] ?? 'Sem nome';
                        $instanceId = $instancia['instanceId'] ?? $instancia['id'] ?? null;
                        $status = $instancia['status'] ?? 'desconhecido';
                        $isOpen = strtolower($status) === 'open';
                        $isConnecting = strtolower($status) === 'connecting';
                        // SEMPRE usar o name da instância para chamadas à API (formato correto v2.3+)
                        // Se não tiver name, usar o ID como fallback
                        $instanceIdentifier = !empty($instanceName) ? $instanceName : ($instanceId ?? '');
                    @endphp
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $instanceName }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    Status: 
                                    <span class="font-medium {{ $isOpen ? 'text-green-600' : ($status === 'connecting' ? 'text-blue-600' : 'text-yellow-600') }}">
                                        {{ ucfirst(strtolower($status)) }}
                                    </span>
                                    @if(isset($instancia['profileName']))
                                    <span class="ml-2 text-gray-500">• {{ $instancia['profileName'] }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex gap-2">
                                @if(!$isOpen)
                                <button onclick="obterQrCode('{{ $instanceIdentifier }}')" 
                                        class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    {{ $isConnecting ? 'Ver QR Code' : 'Gerar QR Code' }}
                                </button>
                                @endif
                                @if($isOpen)
                                <button onclick="abrirModalEnviarMensagem('{{ $instanceIdentifier }}')" 
                                        class="px-3 py-1.5 text-xs bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                    Enviar Mensagem Teste
                                </button>
                                @endif
                                <button onclick="verificarStatus('{{ $instanceIdentifier }}')" 
                                        class="px-3 py-1.5 text-xs bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                    Atualizar Status
                                </button>
                                <button onclick="deletarInstancia('{{ $instanceIdentifier }}')" 
                                        class="px-3 py-1.5 text-xs bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    Deletar
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Nenhuma instância encontrada. Crie uma nova instância para começar.</p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            Atualizar Lista
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Documentação -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Documentação</h3>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p>Para obter suas credenciais da Evolution API:</p>
                <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Acesse o painel de gerenciamento da Evolution API</li>
                    <li>Navegue até a seção de configurações</li>
                    <li>Copie sua API Key Global</li>
                    <li>Configure o webhook para receber notificações</li>
                    <li>Cole as credenciais nos campos acima</li>
                </ol>
                <p class="mt-4">
                    <a href="https://doc.evolution-api.com" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline">
                        Acessar documentação oficial da Evolution API
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
            const url = '{{ url("/whatsapp/webhook") }}';
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

            fetch('{{ route("configuracoes.whatsapp.testar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    whatsapp_api_key: document.getElementById('whatsapp_api_key').value,
                    whatsapp_api_url: document.getElementById('whatsapp_api_url').value,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
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

        function abrirModalCriarInstancia() {
            const modal = document.getElementById('modalCriarInstancia');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function fecharModalCriarInstancia() {
            const modal = document.getElementById('modalCriarInstancia');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function criarInstancia() {
            const instanceName = document.getElementById('nova_instance_name').value;
            const webhookUrl = document.getElementById('nova_webhook_url').value;
            const gerarQrcode = document.getElementById('gerar_qrcode').checked;

            if (!instanceName) {
                alert('Por favor, informe o nome da instância.');
                return;
            }

            const button = document.getElementById('btn-criar-instancia');
            button.disabled = true;
            button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Criando...';

            fetch('{{ route("configuracoes.whatsapp.instancia.criar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    instance_name: instanceName,
                    webhook_url: webhookUrl || null,
                    qrcode: gerarQrcode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    if (gerarQrcode) {
                        setTimeout(() => obterQrCode(instanceName), 1000);
                    }
                    location.reload();
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('✗ Erro ao criar instância.');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = 'Criar Instância';
            });
        }

        function obterQrCode(instanceName) {
            const button = event?.target || document.querySelector(`[onclick*="${instanceName}"]`);
            if (button) {
                button.disabled = true;
                button.innerHTML = 'Gerando...';
            }

            fetch('{{ route("configuracoes.whatsapp.instancia.qrcode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    instance_name: instanceName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.qrcode) {
                    mostrarModalQrCode(data.qrcode, instanceName);
                } else {
                    alert('✗ ' + (data.message || 'Erro ao gerar QR Code'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('✗ Erro ao gerar QR Code.');
            })
            .finally(() => {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = 'Gerar QR Code';
                }
            });
        }

        let qrCodeInterval = null;
        let currentInstanceName = null;

        function mostrarModalQrCode(qrcode, instanceName) {
            const modal = document.getElementById('modalQrCode');
            const qrcodeImg = document.getElementById('qrcode-img');
            
            // Parar qualquer atualização automática anterior
            if (qrCodeInterval) {
                clearInterval(qrCodeInterval);
                qrCodeInterval = null;
            }
            
            currentInstanceName = instanceName;
            
            // Se o QR code já vem em base64, usar diretamente, senão gerar
            const qrcodeData = qrcode.startsWith('data:') ? qrcode : 
                              qrcode.startsWith('http') ? `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrcode)}` :
                              `data:image/png;base64,${qrcode}`;
            
            // Definir src da imagem (adicionar timestamp apenas para URLs, não para base64)
            if (qrcodeData.startsWith('data:')) {
                // Para base64, adicionar timestamp como query string não funciona
                // Usar um objeto URL temporário ou simplesmente definir diretamente
                qrcodeImg.src = qrcodeData;
            } else {
                // Para URLs, adicionar timestamp para evitar cache
                qrcodeImg.src = qrcodeData + (qrcodeData.includes('?') ? '&' : '?') + 't=' + Date.now();
            }
            document.getElementById('qrcode-instance-name').textContent = instanceName;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Atualizar QR code automaticamente a cada 30 segundos (QR codes expiram rápido)
            qrCodeInterval = setInterval(() => {
                atualizarQrCodeNoModal(instanceName);
            }, 30000); // 30 segundos
        }
        
        function atualizarQrCodeNoModal(instanceName) {
            if (!instanceName) return;
            
            fetch('{{ route("configuracoes.whatsapp.instancia.qrcode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    instance_name: instanceName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.qrcode) {
                    const qrcodeImg = document.getElementById('qrcode-img');
                    const qrcodeData = data.qrcode.startsWith('data:') ? data.qrcode : 
                                      data.qrcode.startsWith('http') ? `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data.qrcode)}` :
                                      `data:image/png;base64,${data.qrcode}`;
                    
                    // Atualizar imagem (adicionar timestamp apenas para URLs)
                    if (qrcodeData.startsWith('data:')) {
                        qrcodeImg.src = qrcodeData;
                    } else {
                        qrcodeImg.src = qrcodeData + (qrcodeData.includes('?') ? '&' : '?') + 't=' + Date.now();
                    }
                    
                    console.log('QR Code atualizado automaticamente');
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar QR Code:', error);
            });
        }
        
        function atualizarQrCodeManual() {
            if (currentInstanceName) {
                const qrcodeImg = document.getElementById('qrcode-img');
                qrcodeImg.src = ''; // Limpar imagem atual
                
                fetch('{{ route("configuracoes.whatsapp.instancia.qrcode") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        instance_name: currentInstanceName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.qrcode) {
                        const qrcodeData = data.qrcode.startsWith('data:') ? data.qrcode : 
                                          data.qrcode.startsWith('http') ? `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data.qrcode)}` :
                                          `data:image/png;base64,${data.qrcode}`;
                        
                        // Atualizar imagem (adicionar timestamp apenas para URLs)
                        if (qrcodeData.startsWith('data:')) {
                            qrcodeImg.src = qrcodeData;
                        } else {
                            qrcodeImg.src = qrcodeData + (qrcodeData.includes('?') ? '&' : '?') + 't=' + Date.now();
                        }
                    } else {
                        alert('✗ ' + (data.message || 'Erro ao atualizar QR Code'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('✗ Erro ao atualizar QR Code.');
                });
            }
        }

        function fecharModalQrCode() {
            const modal = document.getElementById('modalQrCode');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            // Parar atualização automática ao fechar o modal
            if (qrCodeInterval) {
                clearInterval(qrCodeInterval);
                qrCodeInterval = null;
            }
            currentInstanceName = null;
        }

        function verificarStatus(instanceName) {
            fetch('{{ route("configuracoes.whatsapp.instancia.status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    instance_name: instanceName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status: ' + (data.data.status || 'desconhecido'));
                    location.reload();
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('✗ Erro ao verificar status.');
            });
        }

        function deletarInstancia(instanceName) {
            if (!confirm(`Tem certeza que deseja deletar a instância "${instanceName}"? Esta ação não pode ser desfeita.`)) {
                return;
            }

            fetch('{{ route("configuracoes.whatsapp.instancia.deletar") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    instance_name: instanceName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    location.reload();
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('✗ Erro ao deletar instância.');
            });
        }

        function atualizarListaInstancias() {
            location.reload();
        }

        function abrirModalEnviarMensagem(instanceName) {
            const modal = document.getElementById('modalEnviarMensagem');
            document.getElementById('mensagem-instance-name').textContent = instanceName;
            document.getElementById('mensagem-instance-name-hidden').value = instanceName;
            document.getElementById('mensagem-numero').value = '';
            document.getElementById('mensagem-texto').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function fecharModalEnviarMensagem() {
            const modal = document.getElementById('modalEnviarMensagem');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function enviarMensagemTeste() {
            const instanceName = document.getElementById('mensagem-instance-name-hidden').value;
            const numero = document.getElementById('mensagem-numero').value.trim();
            const mensagem = document.getElementById('mensagem-texto').value.trim();

            if (!numero) {
                alert('Por favor, informe o número de telefone.');
                document.getElementById('mensagem-numero').focus();
                return;
            }

            if (!mensagem) {
                alert('Por favor, informe a mensagem.');
                document.getElementById('mensagem-texto').focus();
                return;
            }

            // Validar formato básico do número (deve ter entre 12 e 15 dígitos com código do país)
            const numeroLimpo = numero.replace(/\D/g, '');
            if (numeroLimpo.length < 12 || numeroLimpo.length > 15) {
                alert('Número de telefone inválido. O número deve ter entre 12 e 15 dígitos incluindo código do país.\n\nExemplo: 5511999999999 (55 = Brasil, 11 = DDD, 999999999 = número)');
                document.getElementById('mensagem-numero').focus();
                return;
            }

            // Validar se começa com código do país válido (55 para Brasil)
            if (!numeroLimpo.startsWith('55')) {
                if (confirm('O número não começa com código do país 55 (Brasil). Deseja adicionar automaticamente?')) {
                    document.getElementById('mensagem-numero').value = '55' + numeroLimpo.replace(/^55/, '');
                } else {
                    return;
                }
            }

            const button = document.getElementById('btn-enviar-mensagem');
            button.disabled = true;
            button.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Enviando...';

            fetch('{{ route("configuracoes.whatsapp.instancia.enviar-mensagem") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    instance_name: instanceName,
                    numero: numero,
                    mensagem: mensagem
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erro ao enviar mensagem');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    fecharModalEnviarMensagem();
                } else {
                    alert('✗ ' + (data.message || 'Erro ao enviar mensagem'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                const errorMessage = error.message || 'Erro ao enviar mensagem. Verifique se o número está correto e se a instância está conectada.';
                alert('✗ ' + errorMessage);
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = 'Enviar Mensagem';
            });
        }
    </script>

    <!-- Modal Criar Instância -->
    <div id="modalCriarInstancia" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Criar Nova Instância</h3>
                    <button onclick="fecharModalCriarInstancia()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nome da Instância <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nova_instance_name"
                               placeholder="Ex: instancia-principal"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            URL do Webhook (Opcional)
                        </label>
                        <input type="url" 
                               id="nova_webhook_url"
                               placeholder="{{ url('/whatsapp/webhook') }}"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe em branco para usar a URL padrão</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="gerar_qrcode"
                               checked
                               class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-indigo-500">
                        <label for="gerar_qrcode" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Gerar QR Code automaticamente
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button onclick="fecharModalCriarInstancia()" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button id="btn-criar-instancia" onclick="criarInstancia()" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        Criar Instância
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Enviar Mensagem Teste -->
    <div id="modalEnviarMensagem" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Enviar Mensagem de Teste - <span id="mensagem-instance-name"></span></h3>
                    <button onclick="fecharModalEnviarMensagem()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <input type="hidden" id="mensagem-instance-name-hidden" value="">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Número do WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="mensagem-numero"
                               placeholder="5511999999999"
                               pattern="[0-9+]{12,15}"
                               maxlength="15"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Formato: código do país (55) + DDD (2 dígitos) + número (8-9 dígitos)<br>
                            Exemplo: 5511999999999 (Brasil: 55, DDD: 11, número: 999999999)
                        </p>
                        <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                            ⚠️ Importante: Use apenas números que existem no WhatsApp. Números inválidos podem causar desconexão.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Mensagem <span class="text-red-500">*</span>
                        </label>
                        <textarea id="mensagem-texto"
                                  rows="4"
                                  placeholder="Digite sua mensagem de teste aqui..."
                                  class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  required></textarea>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button onclick="fecharModalEnviarMensagem()" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button id="btn-enviar-mensagem" onclick="enviarMensagemTeste()" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        Enviar Mensagem
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div id="modalQrCode" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">QR Code - <span id="qrcode-instance-name"></span></h3>
                    <button onclick="fecharModalQrCode()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Escaneie este QR Code com o WhatsApp para conectar a instância
                    </p>
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                        <img id="qrcode-img" src="" alt="QR Code" class="w-64 h-64 mx-auto">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                        O QR Code expira em alguns minutos. Ele será atualizado automaticamente a cada 30 segundos.
                    </p>
                </div>
                <div class="mt-6 flex gap-3">
                    <button onclick="atualizarQrCodeManual()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Atualizar QR Code
                    </button>
                    <button onclick="fecharModalQrCode()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
