<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pagamento via PicPay</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Transação: {{ $pagamento->numero_transacao }}</p>
            </div>
            <a href="{{ route('pagamentos.show', $pagamento) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        @if($pagamento->picpay_payment_url)
        <!-- Modal de Link de Pagamento -->
        <div id="modalPicPay" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center {{ session('show_modal') ? 'flex' : 'hidden' }}">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] flex flex-col">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Link de Pagamento Criado!</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pagamento PicPay gerado com sucesso</p>
                        </div>
                        <button onclick="fecharModalPicPay()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="space-y-6">
                        <!-- QR Code -->
                        <div class="text-center" id="qrcode-container">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Escaneie o QR Code</h4>
                            @if($pagamento->picpay_qrcode_base64)
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                                <img src="data:image/png;base64,{{ $pagamento->picpay_qrcode_base64 }}" 
                                     alt="QR Code PicPay" 
                                     class="w-64 h-64 mx-auto"
                                     id="qrcode-image">
                            </div>
                            @else
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 inline-block">
                                <canvas id="qrcode-canvas" class="w-64 h-64 mx-auto"></canvas>
                            </div>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Use o app PicPay para escanear</p>
                        </div>

                        <!-- Link de Pagamento -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Link de Pagamento</label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       id="payment-url-input"
                                       value="{{ $pagamento->picpay_payment_url }}" 
                                       readonly
                                       class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-900">
                                <button onclick="copiarLinkPagamento()" 
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Copiar
                                </button>
                            </div>
                        </div>

                        <!-- Botão para abrir -->
                        <div>
                            <a href="{{ $pagamento->picpay_payment_url }}" 
                               target="_blank"
                               class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Abrir Link de Pagamento
                            </a>
                        </div>

                        <!-- Informações -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h4 class="text-xs font-semibold text-blue-900 dark:text-blue-100 mb-2">Informações</h4>
                            <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1">
                                <li><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</li>
                                <li><strong>Cliente:</strong> {{ $pagamento->cliente->nome }}</li>
                                <li><strong>Referência:</strong> {{ $pagamento->numero_transacao }}</li>
                                @if($pagamento->picpay_expires_at)
                                <li><strong>Expira em:</strong> {{ $pagamento->picpay_expires_at->format('d/m/Y H:i') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <button onclick="fecharModalPicPay()" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Status e Valor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Status do Pagamento</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Valor: <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</span></p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pendente' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                            'confirmado' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                            'cancelado' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                        ];
                    @endphp
                    <span id="status-badge" class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$pagamento->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                        {{ ucfirst($pagamento->status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($pagamento->status === 'pendente' && !$pagamento->isPicPayExpirado())
            <!-- QR Code e Informações -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- QR Code -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Escaneie o QR Code</h3>
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center">
                        @if($pagamento->picpay_qrcode_base64)
                            <img src="data:image/png;base64,{{ $pagamento->picpay_qrcode_base64 }}" 
                                 alt="QR Code PicPay" 
                                 class="w-64 h-64 mx-auto"
                                 id="qrcode-img">
                        @else
                            <canvas id="qrcode-main-canvas" class="w-64 h-64 mx-auto"></canvas>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">Use o app PicPay para escanear</p>
                </div>

                <!-- Informações e Ações -->
                <div class="space-y-6">
                    <!-- Informações do Pagamento -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Pagamento</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Cliente:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $pagamento->cliente->nome }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-semibold">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Referência:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $pagamento->numero_transacao }}</dd>
                            </div>
                            @if($pagamento->picpay_expires_at)
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Expira em:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100" id="expires-at">
                                    {{ $pagamento->picpay_expires_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Ações -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações</h3>
                        <div class="space-y-3">
                            @if($pagamento->picpay_payment_url)
                            <a href="{{ $pagamento->picpay_payment_url }}" 
                               target="_blank"
                               class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2.5 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Abrir no PicPay
                            </a>
                            @endif

                            <button onclick="consultarStatus()" 
                                    id="btn-consultar"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-lg text-sm transition-colors">
                                Atualizar Status
                            </button>
                            
                            <form action="{{ route('pagamentos.cancelar-picpay', $pagamento) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este pagamento?');">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2.5 rounded-lg text-sm transition-colors">
                                    Cancelar Pagamento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instruções -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Como pagar:</h4>
                <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800 dark:text-blue-200">
                    <li>Abra o aplicativo PicPay no seu celular</li>
                    <li>Escaneie o QR Code acima ou clique em "Abrir no PicPay"</li>
                    <li>Confirme o pagamento no app</li>
                    <li>O status será atualizado automaticamente</li>
                </ol>
            </div>

        @elseif($pagamento->status === 'confirmado')
            <!-- Pagamento Confirmado -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Pagamento Confirmado!</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">O pagamento foi processado com sucesso.</p>
                    @if($pagamento->data_recebimento)
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-3">Confirmado em: {{ $pagamento->data_recebimento->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>

        @elseif($pagamento->status === 'cancelado' || $pagamento->isPicPayExpirado())
            <!-- Pagamento Cancelado/Expirado -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 dark:bg-red-900 rounded-full mb-4">
                        <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        {{ $pagamento->isPicPayExpirado() ? 'Pagamento Expirado' : 'Pagamento Cancelado' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Este pagamento não pode mais ser processado.</p>
                </div>
            </div>
        @endif

        <!-- Informações Adicionais -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações Relacionadas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cliente</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $pagamento->cliente->nome }}</p>
                </div>
                @if($pagamento->acordo)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Acordo</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $pagamento->acordo->numero_acordo }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dívida</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $pagamento->divida->numero_documento }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data do Pagamento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $pagamento->data_pagamento->format('d/m/Y') }}</p>
                </div>
                @if($pagamento->isPicPay())
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">PicPay Reference ID</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $pagamento->picpay_reference_id }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Abrir modal automaticamente se show_modal estiver na sessão ou se houver link de pagamento
        @if(session('show_modal') || ($pagamento->picpay_payment_url && $pagamento->status === 'pendente'))
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                abrirModalPicPay();
            }, 300);
        });
        @endif

        // Gerar QR Code se não tiver base64
        document.addEventListener('DOMContentLoaded', function() {
            const qrcodeCanvas = document.getElementById('qrcode-canvas');
            const qrcodeMainCanvas = document.getElementById('qrcode-main-canvas');
            const qrcodeImage = document.getElementById('qrcode-image');
            
            // Função para gerar QR Code
            function gerarQRCode(canvas, data) {
                if (!data || !canvas) return;
                
                if (typeof QRCode !== 'undefined') {
                    QRCode.toCanvas(canvas, data, {
                        width: 256,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        }
                    }, function (error) {
                        if (error) {
                            console.error('Erro ao gerar QR Code:', error);
                            // Fallback: usar API externa
                            canvas.parentElement.innerHTML = `
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data)}" 
                                     alt="QR Code PicPay" 
                                     class="w-64 h-64 mx-auto">
                            `;
                        }
                    });
                } else {
                    // Fallback: usar API externa se QRCode não estiver disponível
                    canvas.parentElement.innerHTML = `
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data)}" 
                             alt="QR Code PicPay" 
                             class="w-64 h-64 mx-auto">
                    `;
                }
            }
            
            // Função para gerar QR Code usando API externa
            function gerarQRCodeAPI(canvas, data) {
                if (!data || !canvas) return;
                
                const qrImg = document.createElement('img');
                qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data)}`;
                qrImg.alt = 'QR Code PicPay';
                qrImg.className = 'w-64 h-64 mx-auto';
                qrImg.onload = function() {
                    canvas.parentElement.replaceChild(qrImg, canvas);
                };
                qrImg.onerror = function() {
                    // Se falhar, tentar com biblioteca QRCode
                    if (typeof QRCode !== 'undefined') {
                        gerarQRCode(canvas, data);
                    } else {
                        canvas.parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Erro ao gerar QR Code</p>';
                    }
                };
            }
            
            // Gerar QR Code no modal se necessário
            if (qrcodeCanvas && !qrcodeImage) {
                @php
                    $brcode = session('brcode') ?? ($pagamento->picpay_response['brcode'] ?? '');
                    $paymentUrl = $pagamento->picpay_payment_url ?? '';
                @endphp
                const paymentUrl = '{{ $paymentUrl }}';
                const brcode = '{{ $brcode }}';
                const qrcodeData = brcode || paymentUrl;
                if (qrcodeData) {
                    gerarQRCodeAPI(qrcodeCanvas, qrcodeData);
                }
            }
            
            // Gerar QR Code na página principal se necessário
            if (qrcodeMainCanvas && !qrcodeImage) {
                @php
                    $brcode = session('brcode') ?? ($pagamento->picpay_response['brcode'] ?? '');
                    $paymentUrl = $pagamento->picpay_payment_url ?? '';
                @endphp
                const paymentUrl = '{{ $paymentUrl }}';
                const brcode = '{{ $brcode }}';
                const qrcodeData = brcode || paymentUrl;
                if (qrcodeData) {
                    gerarQRCodeAPI(qrcodeMainCanvas, qrcodeData);
                }
            }
        });

        function abrirModalPicPay() {
            const modal = document.getElementById('modalPicPay');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function fecharModalPicPay() {
            const modal = document.getElementById('modalPicPay');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function copiarLinkPagamento() {
            const input = document.getElementById('payment-url-input');
            input.select();
            input.setSelectionRange(0, 99999); // Para mobile
            
            navigator.clipboard.writeText(input.value).then(() => {
                // Feedback visual
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
                // Fallback
                document.execCommand('copy');
                alert('Link copiado para a área de transferência!');
            });
        }

        // Fechar modal ao clicar fora
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalPicPay');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        fecharModalPicPay();
                    }
                });
            }
        });

        function consultarStatus() {
            const button = document.getElementById('btn-consultar');
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Consultando...';

            fetch('{{ route("pagamentos.consultar-picpay", $pagamento) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar badge de status
                        const statusBadge = document.getElementById('status-badge');
                        const statusText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        
                        // Remover classes antigas
                        statusBadge.className = 'px-3 py-1 text-sm font-semibold rounded-full';
                        
                        // Adicionar classes baseadas no status
                        if (data.status === 'confirmado') {
                            statusBadge.classList.add('bg-green-100', 'dark:bg-green-900', 'text-green-800', 'dark:text-green-200');
                        } else if (data.status === 'cancelado') {
                            statusBadge.classList.add('bg-red-100', 'dark:bg-red-900', 'text-red-800', 'dark:text-red-200');
                        } else {
                            statusBadge.classList.add('bg-yellow-100', 'dark:bg-yellow-900', 'text-yellow-800', 'dark:text-yellow-200');
                        }
                        
                        statusBadge.textContent = statusText;

                        // Se confirmado, recarregar a página após 2 segundos
                        if (data.status === 'confirmado') {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    } else {
                        alert('Erro ao consultar status: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao consultar status do pagamento');
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = originalText;
                });
        }

        // Auto-atualizar status a cada 30 segundos se estiver pendente
        @if($pagamento->status === 'pendente' && !$pagamento->isPicPayExpirado())
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                fetch('{{ route("pagamentos.consultar-picpay", $pagamento) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.status !== '{{ $pagamento->status }}') {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Erro ao auto-consultar:', error));
            }
        }, 30000); // 30 segundos
        @endif
    </script>
</x-app-layout>
