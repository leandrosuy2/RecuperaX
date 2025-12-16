<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Títulos Quitados</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Relatório de títulos quitados (baixados com pagamento)</p>
                @if(!$is_admin)
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">* Exibindo somente registros do operador/supervisor {{ $user_name }}</p>
                @endif
            </div>
            <a href="{{ route('titulos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar para Títulos
            </a>
        </div>

        <!-- Soma Total -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Recebido</p>
                    <p class="text-3xl font-bold">R$ {{ number_format($soma_total, 2, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90">Registros encontrados</p>
                    <p class="text-2xl font-bold">{{ $paginator->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <!-- Período -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                    <select name="tipo" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="parcela" {{ request('tipo') == 'parcela' ? 'selected' : '' }}>Parcela</option>
                        <option value="quitacao" {{ request('tipo') == 'quitacao' ? 'selected' : '' }}>Quitação</option>
                    </select>
                </div>

                <!-- Devedor -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Devedor</label>
                    <input type="text" name="devedor" value="{{ request('devedor') }}" placeholder="Nome do devedor"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Empresa -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                    <input type="text" name="empresa" value="{{ request('empresa') }}" placeholder="Nome fantasia"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Valor Mínimo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Valor Mínimo</label>
                    <input type="text" name="valor_min" value="{{ request('valor_min') }}" placeholder="0,00"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Valor Máximo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Valor Máximo</label>
                    <input type="text" name="valor_max" value="{{ request('valor_max') }}" placeholder="0,00"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                @if($is_admin)
                <!-- Operador (apenas admin) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Operador</label>
                    <select name="operador" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        @foreach($operadores as $op)
                        <option value="{{ $op->operador }}" {{ request('operador') == $op->operador ? 'selected' : '' }}>{{ $op->operador }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supervisor (apenas admin) -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor</label>
                    <select name="supervisor" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        @foreach($supervisores as $sup)
                        <option value="{{ $sup->supervisor }}" {{ request('supervisor') == $sup->supervisor ? 'selected' : '' }}>{{ $sup->supervisor }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Botões -->
                <div class="flex items-end gap-2 md:col-span-2 lg:col-span-3 xl:col-span-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['data_inicio', 'data_fim', 'tipo', 'devedor', 'empresa', 'valor_min', 'valor_max', 'operador', 'supervisor']))
                    <a href="{{ route('titulos.quitados') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">
                        Limpar
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Baixa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Vencimento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Supervisor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($paginator->items() as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm">
                                @if($item['idTituloRef'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Parcela</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Quitação</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['data_baixa'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['data_vencimento'] }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100 text-right">R$ {{ number_format($item['valor_recebido'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['nome'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['cpf'] ?: $item['cnpj'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['empresa'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['operador'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['supervisor'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum título quitado encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden">
                @forelse($paginator->items() as $item)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                            @if($item['idTituloRef'])
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Parcela</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Quitação</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100" data-label="Valor Recebido">
                                R$ {{ number_format($item['valor_recebido'], 2, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="Data Baixa">Data Baixa:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['data_baixa'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="Data Vencimento">Data Vencimento:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['data_vencimento'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="Devedor">Devedor:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['nome'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="CPF/CNPJ">CPF/CNPJ:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['cpf'] ?: $item['cnpj'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="Empresa">Empresa:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['empresa'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="Operador">Operador:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['operador'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400" data-label="Supervisor">Supervisor:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $item['supervisor'] }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Nenhum título quitado encontrado
                </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if($paginator->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                {{ $paginator->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 767px) {
            .md\:hidden th {
                display: none;
            }
        }
    </style>
</x-app-layout>
