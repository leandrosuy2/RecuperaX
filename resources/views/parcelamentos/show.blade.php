<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Parcela #{{ $parcelamento->parcela_numero }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Acordo #{{ $parcelamento->acordo_id }}</p>
            </div>
            <a href="{{ route('parcelamentos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <!-- Informações da Parcela -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações da Parcela</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Número da Parcela</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $parcelamento->parcela_numero }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Devedor</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $parcelamento->acordo->nome_devedor }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Empresa</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $parcelamento->acordo->empresa ? ($parcelamento->acordo->empresa->nome_fantasia ?? $parcelamento->acordo->empresa->razao_social) : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($parcelamento->valor, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Vencimento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $parcelamento->data_vencimento->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-sm">
                        @if($parcelamento->status === 'PAGO')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Pago</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pendente</span>
                        @endif
                    </p>
                </div>
                @if($parcelamento->status === 'PAGO')
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Pagamento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $parcelamento->data_baixa->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Forma de Pagamento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $parcelamento->forma_pagamento ?? '-' }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Ações -->
        @if($parcelamento->status === 'PENDENTE')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações</h2>
            <div class="flex gap-3">
                <a href="{{ route('parcelamentos.pagar', $parcelamento) }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                    Pagar Parcela
                </a>
            </div>
        </div>
        @endif

        <!-- Comprovante -->
        @if($parcelamento->comprovante)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Comprovante</h2>
            <a href="{{ route('parcelamentos.baixar-comprovante', $parcelamento) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Baixar Comprovante
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
