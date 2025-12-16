<x-app-layout>
    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(234, 179, 8, 0.5); }
            50% { box-shadow: 0 0 30px rgba(234, 179, 8, 0.8); }
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .ranking-card {
            transition: all 0.3s ease;
        }
        .ranking-card:hover {
            transform: translateY(-2px);
        }
    </style>
    <div class="space-y-6">
        <!-- Cards de KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Pendentes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Pendentes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($pendentes, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quitados -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Quitados</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($quitados, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Negociados -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Negociados</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($negociados, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Total de Clientes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Total de Clientes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalClientes, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Negociados em Atraso -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Negociados em Atraso</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($negociadosEmAtraso, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quitados Hoje -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors cursor-pointer hover:shadow-md" onclick="abrirModalQuitados()">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Quitados Hoje</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($quitadosHoje ?? 0, 2, ',', '.') }}</p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1">Clique para detalhes</p>
                    </div>
                </div>
            </div>

            <!-- Negociados Hoje -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors cursor-pointer hover:shadow-md" onclick="abrirModalNegociados()">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Negociados Hoje</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($negociadosHoje ?? 0, 2, ',', '.') }}</p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1">Clique para detalhes</p>
                    </div>
                </div>
            </div>

            <!-- Ranking Operadores -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors cursor-pointer hover:shadow-md" onclick="abrirModalRanking()">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ranking Operadores</p>
                        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Ver Ranking</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Clique para ver</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Busca Rápida -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Busca Rápida</h2>
            <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                <input type="text" name="busca_rapida" value="{{ $buscaRapida }}" 
                       placeholder="Buscar por nome, CPF, CNPJ, empresa..." 
                       class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Buscar
                </button>
                @if($buscaRapida)
                <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Limpar
                </a>
                @endif
            </form>
            
            @if($resultadosBusca->count() > 0)
            <div class="mt-4 space-y-2">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Resultados da busca:</p>
                @foreach($resultadosBusca as $devedor)
                <a href="{{ route('devedores.show', $devedor) }}" class="block p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $devedor->nome_completo }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $devedor->documento }}</p>
                    @if($devedor->empresa)
                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $devedor->empresa->nome_fantasia ?? $devedor->empresa->razao_social }}</p>
                    @endif
                </a>
                @endforeach
            </div>
            @endif
        </div>


        <!-- Agenda de Trabalho do Dia - Pendentes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Agenda de Trabalho do Dia - Pendentes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome da Mãe</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Telefone</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($agendaPendentes as $titulo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $titulo->devedor ? ($titulo->devedor->nome ?? $titulo->devedor->razao_social ?? '-') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $titulo->empresa ? ($titulo->empresa->id . ' - ' . ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social)) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $titulo->devedor ? ($titulo->devedor->nome_mae ?? '-') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $titulo->operador ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $titulo->devedor ? ($titulo->devedor->cpf ?? $titulo->devedor->cnpj ?? '-') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $titulo->devedor ? ($titulo->devedor->telefone ?? '-') : '-' }}
                            </td>
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
                                    <form action="{{ route('titulos.finalizar', $titulo) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                                title="Finalizar título"
                                                onclick="return confirm('Deseja finalizar este título?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <button type="button" 
                                            onclick="abrirModalBaixar({{ $titulo->id }})" 
                                            class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                            title="Baixar título">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum título pendente encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($agendaPendentes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $agendaPendentes->appends(request()->except('agenda_page'))->links() }}
            </div>
            @endif
        </div>

        <!-- Agendamentos do Dia -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Agendamentos do Dia</h2>
            </div>
            <div class="p-4">
                @if($agendamentosPendentes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Telefone</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Retorno</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Assunto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($agendamentosPendentes as $agendamento)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $agendamento->devedor ? ($agendamento->devedor->nome ?? $agendamento->devedor->razao_social ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $agendamento->devedor ? ($agendamento->devedor->cpf ?? $agendamento->devedor->cnpj ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $agendamento->empresa ? ($agendamento->empresa->nome_fantasia ?? $agendamento->empresa->razao_social ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $agendamento->telefone ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $agendamento->data_retorno ? $agendamento->data_retorno->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $agendamento->assunto ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $agendamento->operador ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhum agendamento para hoje.</p>
                @endif
            </div>
        </div>

        <!-- Negociados em Atraso -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Negociados em Atraso</h2>
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Início</label>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" 
                               class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Fim</label>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" 
                               class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data de Vencimento</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($negociadosAtraso as $titulo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->devedor ? ($titulo->devedor->nome ?? $titulo->devedor->razao_social ?? '-') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->empresa ? ($titulo->empresa->id . ' - ' . ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social)) : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->operador ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor_com_juros, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                    Negociado em atraso
                                </span>
                            </td>
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
                                    <form action="{{ route('titulos.finalizar', $titulo) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                                title="Finalizar título"
                                                onclick="return confirm('Deseja finalizar este título?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <button type="button" 
                                            onclick="abrirModalBaixar({{ $titulo->id }})" 
                                            class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                            title="Baixar título">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum título negociado em atraso encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($negociadosAtraso->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $negociadosAtraso->appends(request()->except('negociados_page'))->links() }}
            </div>
            @endif
        </div>

        <!-- Últimos Clientes Cadastrados -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Últimos Clientes Cadastrados</h2>
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Cadastro (Início)</label>
                        <input type="date" name="data_cadastro_inicio" value="{{ request('data_cadastro_inicio') }}" 
                               class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Cadastro (Fim)</label>
                        <input type="date" name="data_cadastro_fim" value="{{ request('data_cadastro_fim') }}" 
                               class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @if(auth()->user()->canViewAllDividas())
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                        <select name="empresa_id" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas</option>
                            @foreach(\App\Models\Empresa::all() as $empresa)
                            <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                {{ $empresa->id }} - {{ $empresa->nome_fantasia ?? $empresa->razao_social }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data de Cadastro</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lojista</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($ultimosClientes as $devedor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $devedor->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $devedor->nome_completo }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $devedor->documento }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $devedor->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                @if($devedor->empresa)
                                    {{ $devedor->empresa->id }} - {{ $devedor->empresa->nome_fantasia ?? $devedor->empresa->razao_social }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum devedor encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ultimosClientes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $ultimosClientes->appends(request()->except('clientes_page'))->links() }}
            </div>
            @endif
        </div>

        <!-- Parcelamentos Atrasados -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Parcelamentos Atrasados</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Parcelas pendentes com vencimento até hoje</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Parcela</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Vencimento</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($parcelamentosAtrasados ?? [] as $parcela)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $parcela->parcela_numero }}/{{ $parcela->acordo->qtde_prc ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $parcela->acordo->devedor ? ($parcela->acordo->devedor->nome ?? $parcela->acordo->devedor->razao_social ?? '-') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $parcela->acordo->empresa ? ($parcela->acordo->empresa->nome_fantasia ?? $parcela->acordo->empresa->razao_social ?? '-') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $parcela->data_vencimento ? $parcela->data_vencimento->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">
                                R$ {{ number_format($parcela->valor ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    Atrasado
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($parcela->acordo->titulo)
                                <a href="{{ route('titulos.show', $parcela->acordo->titulo) }}" 
                                   class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                   title="Ver título">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum parcelamento em atraso encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Últimas Movimentações -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Últimas Movimentações</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Últimos 10 acordos criados</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID Acordo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID Título</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Entrada</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Entrada</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contato</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($ultimasMovimentacoes ?? [] as $acordo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $acordo->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $acordo->devedor ? ($acordo->devedor->nome ?? $acordo->devedor->razao_social ?? '-') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $acordo->titulo_id ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">
                                R$ {{ number_format($acordo->entrada ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $acordo->data_entrada ? $acordo->data_entrada->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $acordo->contato ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma movimentação encontrada
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Quitados Hoje -->
    <div id="modalQuitados" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quitados Hoje - Detalhes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total: R$ {{ number_format($quitadosHoje ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <button onclick="fecharModalQuitados()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Baixa</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($quitadosHojeDetalhes ?? [] as $titulo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->devedor ? ($titulo->devedor->nome ?? $titulo->devedor->razao_social ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->devedor ? ($titulo->devedor->cpf ?? $titulo->devedor->cnpj ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->empresa ? ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->data_baixa ? $titulo->data_baixa->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                    R$ {{ number_format($titulo->valorRecebido ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Nenhum título quitado hoje
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Negociados Hoje -->
    <div id="modalNegociados" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Negociados Hoje - Detalhes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total: R$ {{ number_format($negociadosHoje ?? 0, 2, ',', '.') }}</p>
                    </div>
                    <button onclick="fecharModalNegociados()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($negociadosHojeDetalhes ?? [] as $titulo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->devedor ? ($titulo->devedor->nome ?? $titulo->devedor->razao_social ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->devedor ? ($titulo->devedor->cpf ?? $titulo->devedor->cnpj ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->empresa ? ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->created_at ? $titulo->created_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                    R$ {{ number_format($titulo->valor ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Nenhum título negociado hoje
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ranking Operadores -->
    <div id="modalRanking" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[90vh] flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-gray-800 dark:to-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Ranking Operadores - Títulos Quitados</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Ranking por quantidade de títulos quitados e valor recebido</p>
                        </div>
                    </div>
                    <button onclick="fecharModalRanking()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Filtros -->
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Baixa (Início)</label>
                        <input type="date" name="data_ranking_inicio" value="{{ request('data_ranking_inicio') }}" 
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Baixa (Fim)</label>
                        <input type="date" name="data_ranking_fim" value="{{ request('data_ranking_fim') }}" 
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Operador</label>
                        <select name="operador_filtro" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            @foreach($operadores ?? [] as $operador)
                            <option value="{{ $operador }}" {{ request('operador_filtro') == $operador ? 'selected' : '' }}>
                                {{ $operador }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Filtrar
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                            Limpar
                        </a>
                    </div>
                </form>
                
                @if(isset($totalRecebidoGeral))
                <div class="mt-3 p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Total Recebido: <span class="text-indigo-600 dark:text-indigo-400">R$ {{ number_format($totalRecebidoGeral, 2, ',', '.') }}</span>
                    </p>
                </div>
                @endif
            </div>
            <div class="p-4 overflow-y-auto flex-1">
                <div class="space-y-3">
                    @php
                        $maxTotal = $rankingOperadores->first()->total ?? 1;
                        $positions = [
                            1 => ['color' => 'from-yellow-400 to-yellow-600', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-300 dark:border-yellow-700', 'icon' => '🥇', 'glow' => 'shadow-yellow-500/50'],
                            2 => ['color' => 'from-gray-300 to-gray-500', 'bg' => 'bg-gray-50 dark:bg-gray-700/50', 'border' => 'border-gray-300 dark:border-gray-600', 'icon' => '🥈', 'glow' => 'shadow-gray-500/50'],
                            3 => ['color' => 'from-orange-400 to-orange-600', 'bg' => 'bg-orange-50 dark:bg-orange-900/20', 'border' => 'border-orange-300 dark:border-orange-700', 'icon' => '🥉', 'glow' => 'shadow-orange-500/50'],
                            4 => ['color' => 'from-blue-400 to-blue-600', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-300 dark:border-blue-700', 'icon' => '4️⃣', 'glow' => 'shadow-blue-500/50'],
                            5 => ['color' => 'from-purple-400 to-purple-600', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'border' => 'border-purple-300 dark:border-purple-700', 'icon' => '5️⃣', 'glow' => 'shadow-purple-500/50'],
                        ];
                    @endphp
                    @forelse($rankingOperadores ?? [] as $index => $item)
                        @if($index >= 10) @break @endif
                        @php
                            $position = $index + 1;
                            $style = $positions[$position] ?? ['color' => 'from-gray-400 to-gray-600', 'bg' => 'bg-gray-50 dark:bg-gray-700/50', 'border' => 'border-gray-300 dark:border-gray-600', 'icon' => '🏅', 'glow' => 'shadow-gray-500/50'];
                            $percentage = $maxTotal > 0 ? ($item->total / $maxTotal) * 100 : 0;
                        @endphp
                        <div class="ranking-card relative {{ $style['bg'] }} {{ $style['border'] }} border rounded-lg p-3 hover:shadow-md {{ $style['glow'] }} {{ $position <= 3 ? 'animate-pulse-glow' : '' }}">
                            <div class="flex items-center gap-3">
                                <!-- Posição com Troféu/Medalha -->
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br {{ $style['color'] }} flex items-center justify-center shadow-md {{ $style['glow'] }}">
                                            <span class="text-xl">{{ $style['icon'] }}</span>
                                        </div>
                                        @if($position <= 3)
                                        <div class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center animate-pulse">
                                            <svg class="w-2.5 h-2.5 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Informações do Operador -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $item->operador ?? 'Sem operador' }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $position }}º lugar
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-base font-bold text-gray-900 dark:text-gray-100">
                                                {{ number_format($item->total ?? 0, 0, ',', '.') }} <span class="text-xs font-normal">títulos</span>
                                            </div>
                                            <div class="text-sm font-semibold bg-gradient-to-r {{ $style['color'] }} bg-clip-text text-transparent">
                                                R$ {{ number_format($item->valor_total ?? 0, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Barra de Progresso -->
                                    <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r {{ $style['color'] }} rounded-full transition-all duration-1000 ease-out" 
                                             style="width: {{ $percentage }}%">
                                        </div>
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Decoração de fundo -->
                            @if($position <= 3)
                            <div class="absolute top-0 right-0 w-20 h-20 opacity-5">
                                <svg class="w-full h-full text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Nenhum operador encontrado</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Baixar Título -->
    <div id="modalBaixar" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Baixar Título</h3>
                <form id="formBaixar" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Recebido</label>
                        <input type="number" name="valor_recebido" step="0.01" min="0" required
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

        // Funções para abrir/fechar modais
        function abrirModalQuitados() {
            document.getElementById('modalQuitados').classList.remove('hidden');
            document.getElementById('modalQuitados').classList.add('flex');
        }

        function fecharModalQuitados() {
            document.getElementById('modalQuitados').classList.add('hidden');
            document.getElementById('modalQuitados').classList.remove('flex');
        }

        function abrirModalNegociados() {
            document.getElementById('modalNegociados').classList.remove('hidden');
            document.getElementById('modalNegociados').classList.add('flex');
        }

        function fecharModalNegociados() {
            document.getElementById('modalNegociados').classList.add('hidden');
            document.getElementById('modalNegociados').classList.remove('flex');
        }

        function abrirModalRanking() {
            document.getElementById('modalRanking').classList.remove('hidden');
            document.getElementById('modalRanking').classList.add('flex');
            // Animação das barras de progresso
            setTimeout(() => {
                const barras = document.querySelectorAll('#modalRanking [style*="width"]');
                barras.forEach((barra, index) => {
                    const larguraOriginal = barra.style.width;
                    barra.style.width = '0%';
                    setTimeout(() => {
                        barra.style.transition = 'width 1.5s ease-out';
                        barra.style.width = larguraOriginal;
                    }, index * 200);
                });
            }, 100);
        }

        function fecharModalRanking() {
            document.getElementById('modalRanking').classList.add('hidden');
            document.getElementById('modalRanking').classList.remove('flex');
        }

        // Fechar modais ao clicar fora
        document.getElementById('modalQuitados')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalQuitados();
            }
        });

        document.getElementById('modalNegociados')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalNegociados();
            }
        });

        document.getElementById('modalRanking')?.addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalRanking();
            }
        });

        // Fechar modais com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModalQuitados();
                fecharModalNegociados();
                fecharModalRanking();
            }
        });

    </script>
</x-app-layout>
