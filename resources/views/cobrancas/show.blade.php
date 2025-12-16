<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Cobrança #{{ $cobranca->id }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Detalhes da cobrança</p>
            </div>
            <a href="{{ route('cobrancas.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <!-- Informações da Cobrança -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações da Cobrança</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Empresa</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $cobranca->empresa ? ($cobranca->empresa->nome_fantasia ?? $cobranca->empresa->razao_social) : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data da Cobrança</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $cobranca->data_cobranca->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor da Comissão</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($cobranca->valor_comissao, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-sm">
                        <span class="px-2 py-1 text-xs rounded-full {{ $cobranca->pago ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                            {{ $cobranca->pago ? 'Pago' : 'Pendente' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Anexo -->
        @if($cobranca->documento || $cobranca->link)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Anexo</h2>
            @if($cobranca->documento)
            <a href="{{ route('cobrancas.baixar-documento', $cobranca) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Baixar Documento
            </a>
            @elseif($cobranca->link)
            <a href="{{ $cobranca->link }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Abrir Link
            </a>
            @endif
        </div>
        @endif

        <!-- Ações -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações</h2>
            <form action="{{ route('cobrancas.atualizar-pago', $cobranca) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white {{ $cobranca->pago ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} rounded-lg transition-colors">
                    {{ $cobranca->pago ? 'Marcar como Pendente' : 'Marcar como Pago' }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
