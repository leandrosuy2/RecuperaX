<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Dívidas</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gerenciar dívidas e cobranças</p>
            </div>
            @if(auth()->user()->canViewAllDividas())
            <a href="{{ route('dividas.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Dívida
            </a>
            @endif
        </div>

        <!-- Pesquisa e Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="space-y-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº documento, cliente..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="sm:w-48">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Credor</label>
                        <select name="credor_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            <option value="">Todos</option>
                            @foreach($credores as $credor)
                            <option value="{{ $credor->id }}" {{ request('credor_id') == $credor->id ? 'selected' : '' }}>
                                {{ $credor->razao_social }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            <option value="">Todos</option>
                            <option value="a_vencer" {{ request('status') == 'a_vencer' ? 'selected' : '' }}>A vencer</option>
                            <option value="vencida" {{ request('status') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                            <option value="em_negociacao" {{ request('status') == 'em_negociacao' ? 'selected' : '' }}>Em negociação</option>
                            <option value="quitada" {{ request('status') == 'quitada' ? 'selected' : '' }}>Quitada</option>
                        </select>
                    </div>
                    <div class="sm:w-48">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Consultor</label>
                        <select name="consultor_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            <option value="">Todos</option>
                            @foreach($consultores as $consultor)
                            <option value="{{ $consultor->id }}" {{ request('consultor_id') == $consultor->id ? 'selected' : '' }}>
                                {{ $consultor->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    @if(request()->anyFilled(['search', 'credor_id', 'status', 'consultor_id']))
                    <a href="{{ route('dividas.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">Limpar</a>
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
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Credor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Vencimento</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($dividas as $divida)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $divida->id }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $divida->cliente ? $divida->cliente->nome : '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $divida->cliente ? ($divida->cliente->cpf_cnpj ?? '-') : '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden lg:table-cell">{{ $divida->credor ? ($divida->credor->nome_fantasia ?? $divida->credor->razao_social) : '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($divida->valor_atual, 2, ',', '.') }}</div>
                                @if($divida->valor_atual != $divida->valor_original)
                                <div class="text-xs text-gray-500 dark:text-gray-400">Original: R$ {{ number_format($divida->valor_original, 2, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden md:table-cell">
                                <div>{{ $divida->data_vencimento->format('d/m/Y') }}</div>
                                @if($divida->status == 'vencida')
                                <div class="text-xs text-red-600 dark:text-red-400">{{ $divida->data_vencimento->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $divida->status_color }}">
                                    {{ $divida->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('dividas.show', $divida) }}" 
                                       class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                       title="Ver detalhes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(auth()->user()->canViewAllDividas() || (auth()->user()->isConsultor() && $divida->consultor_id == auth()->id()))
                                    <a href="{{ route('dividas.edit', $divida) }}" 
                                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma dívida encontrada
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($dividas->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $dividas->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
