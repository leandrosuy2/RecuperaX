<x-app-layout>
    <div class="space-y-6">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detalhes do Título</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ $titulo->id }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar ao Dashboard
            </a>
        </div>

        <!-- Informações do Título -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Título</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Número do Título</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->num_titulo ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Emissão</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataEmissao ? $titulo->dataEmissao->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Vencimento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Original</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Juros</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->juros, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor com Juros</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor_com_juros, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Recebido</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valorRecebido ?? 0, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-sm">
                        @if($titulo->statusBaixa === 0 || $titulo->statusBaixa === null)
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pendente</span>
                        @elseif($titulo->statusBaixa === 2)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Quitado</span>
                        @elseif($titulo->statusBaixa === 3)
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Negociado</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ $titulo->statusBaixa }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Operador</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->operador ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Última Ação</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->ultima_acao ? $titulo->ultima_acao->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Informações do Devedor -->
        @if($titulo->devedor)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Devedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->devedor->nome ?? $titulo->devedor->razao_social ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CPF/CNPJ</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->devedor->documento ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome da Mãe</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->devedor->nome_mae ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Informações da Empresa -->
        @if($titulo->empresa)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações da Empresa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">ID</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->empresa->id }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome Fantasia</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->empresa->nome_fantasia ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Razão Social</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->empresa->razao_social ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Acordos -->
        @if($titulo->acordos->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Acordos</h2>
            <div class="space-y-4">
                @foreach($titulo->acordos as $acordo)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Total</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($acordo->valor_total_negociacao ?? 0, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data do Acordo</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $acordo->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Ações -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações</h2>
            <div class="flex gap-3">
                @if($titulo->isPendente())
                <form action="{{ route('titulos.finalizar', $titulo) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors" 
                            title="Finalizar título"
                            onclick="return confirm('Deseja finalizar este título?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Finalizar
                    </button>
                </form>
                <button type="button" 
                        onclick="abrirModalBaixar({{ $titulo->id }})" 
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
                        title="Baixar título">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Baixar
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para Baixar Título -->
    <div id="modalBaixar" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Baixar Título</h3>
                <form id="formBaixar" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Recebido</label>
                        <input type="number" name="valor_recebido" step="0.01" min="0" required
                               value="{{ $titulo->valor_com_juros }}"
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de Baixa</label>
                        <input type="date" name="data_baixa" value="{{ date('Y-m-d') }}" required
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observação (opcional)</label>
                        <textarea name="observacao" rows="3"
                                  class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="fecharModalBaixar()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            Confirmar Baixa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalBaixar(tituloId) {
            const modal = document.getElementById('modalBaixar');
            const form = document.getElementById('formBaixar');
            form.action = `/titulos/${tituloId}/baixar`;
            modal.classList.remove('hidden');
        }

        function fecharModalBaixar() {
            const modal = document.getElementById('modalBaixar');
            modal.classList.add('hidden');
        }

        // Fechar modal ao clicar fora
        document.getElementById('modalBaixar').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalBaixar();
            }
        });
    </script>
</x-app-layout>
