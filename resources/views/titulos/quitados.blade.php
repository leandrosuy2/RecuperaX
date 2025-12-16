<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Títulos Quitados</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Lista de títulos que foram quitados</p>
            </div>
            <a href="{{ route('titulos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar para Títulos
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº título, devedor..." 
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Baixa Início</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Baixa Fim</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    @if(request()->anyFilled(['search', 'data_inicio', 'data_fim']))
                    <a href="{{ route('titulos.quitados') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Total Quitado</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                    R$ {{ number_format($titulos->sum('valorRecebido') ?? $titulos->sum('valor'), 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Quantidade</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $titulos->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Média por Título</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                    R$ {{ number_format($titulos->count() > 0 ? ($titulos->sum('valorRecebido') ?? $titulos->sum('valor')) / $titulos->count() : 0, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nº Título</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vencimento</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Original</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Baixa</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($titulos as $titulo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $titulo->num_titulo ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->devedor ? $titulo->devedor->nome_completo : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden md:table-cell">
                                {{ $titulo->empresa ? ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">R$ {{ number_format($titulo->valorRecebido ?? $titulo->valor, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->data_baixa ? $titulo->data_baixa->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('titulos.show', $titulo) }}" 
                                       class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                       title="Ver detalhes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('titulos.gerar-recibo', $titulo) }}" 
                                       class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                       title="Gerar recibo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum título quitado encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($titulos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $titulos->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
