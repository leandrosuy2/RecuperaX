<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Pagamentos Realizados - PicPay</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Visualize todos os pagamentos confirmados via PicPay</p>
            </div>
            <a href="{{ route('pagamentos.index') }}" class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Total Recebido</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Este Mês</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">R$ {{ number_format($totalMes, 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Total de Pagamentos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $pagamentos->total() }}</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pesquisa e Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº transação, reference ID, cliente..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                </div>
                <div class="sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    @if(request()->anyFilled(['search', 'data_inicio', 'data_fim']))
                    <a href="{{ route('pagamentos.realizados') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reference ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nº Transação</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Recebimento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($pagamentos as $pagamento)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <span class="font-mono text-xs text-gray-600 dark:text-gray-400">{{ $pagamento->reference_id ?? $pagamento->picpay_reference_id }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $pagamento->numero_transacao ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                @if(is_object($pagamento->cliente) && isset($pagamento->cliente->nome))
                                    {{ $pagamento->cliente->nome }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                @if($pagamento->data_recebimento)
                                    {{ $pagamento->data_recebimento->format('d/m/Y H:i') }}
                                @elseif($pagamento->data_pagamento)
                                    {{ $pagamento->data_pagamento->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    Confirmado
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    @if($pagamento->pagamento_id)
                                    <a href="{{ route('pagamentos.show', $pagamento->pagamento_id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="Ver Detalhes">
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Pagamento não encontrado no banco">
                                    @endif
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    @if($pagamento->pagamento_id)
                                    </a>
                                    @else
                                    </span>
                                    @endif
                                    @if($pagamento->picpay_payment_url)
                                    <a href="{{ $pagamento->picpay_payment_url }}" target="_blank" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" title="Ver no PicPay">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum pagamento realizado encontrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($pagamentos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $pagamentos->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
