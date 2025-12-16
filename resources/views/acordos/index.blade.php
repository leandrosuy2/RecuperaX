<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Acordos</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gerenciar acordos de pagamento</p>
            </div>
        </div>

        <!-- Pesquisa e Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº acordo, cliente..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        <option value="">Todos</option>
                        <option value="pendente_aprovacao" {{ request('status') == 'pendente_aprovacao' ? 'selected' : '' }}>Pendente Aprovação</option>
                        <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="quitado" {{ request('status') == 'quitado' ? 'selected' : '' }}>Quitado</option>
                        <option value="quebrado" {{ request('status') == 'quebrado' ? 'selected' : '' }}>Quebrado</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('acordos.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">Limpar</a>
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
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nº Acordo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Valor Acordado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Parcelas</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($acordos as $acordo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $acordo->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $acordo->nome_devedor }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden md:table-cell">R$ {{ number_format($acordo->valor_total_negociacao, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden lg:table-cell">{{ $acordo->qtde_prc }}x de R$ {{ number_format($acordo->valor_por_parcela, 2, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $parcelasPagas = $acordo->parcelas->where('status', 'PAGO')->count();
                                    $totalParcelas = $acordo->parcelas->count();
                                    $status = $totalParcelas > 0 && $parcelasPagas == $totalParcelas ? 'quitado' : ($parcelasPagas > 0 ? 'ativo' : 'pendente');
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $status === 'ativo' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : ($status === 'quitado' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('acordos.show', $acordo) }}" 
                                       class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                       title="Ver detalhes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum acordo encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($acordos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $acordos->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
