<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Devedores</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gerenciar devedores do sistema</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('devedores.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Novo Devedor
                </a>
                <a href="#" onclick="baixarModelo()" class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Baixar Modelo
                </a>
                <a href="#" onclick="abrirModalImportacao()" class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Importar
                </a>
            </div>
        </div>

        <!-- Dashboard de Totais -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pendente</p>
                        <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $totaisFormatados['pendente'] }}</p>
                    </div>
                    <div class="text-yellow-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Negociado</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $totaisFormatados['negociado'] }}</p>
                    </div>
                    <div class="text-blue-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Quitado</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $totaisFormatados['quitado'] }}</p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totaisFormatados['total'] }}</p>
                    </div>
                    <div class="text-gray-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A2.01 2.01 0 0 0 18.06 7H15V4c0-1.11-.89-2-2-2H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h7v2H8v2h8v-2h-2v-2h.5c.83 0 1.5-.67 1.5-1.5V18h2zm-7-2H4V6h9v6H9v2h4v2H9v2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, CPF, CNPJ, telefone..."
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="lg:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                    <select name="empresa_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas as empresas</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nome_fantasia ?? $empresa->razao_social }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:w-32">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                    <select name="tipo_pessoa" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="F" {{ request('tipo_pessoa') == 'F' ? 'selected' : '' }}>Física</option>
                        <option value="J" {{ request('tipo_pessoa') == 'J' ? 'selected' : '' }}>Jurídica</option>
                    </select>
                </div>

                <div class="lg:w-36">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="negociado" {{ request('status') == 'negociado' ? 'selected' : '' }}>Negociado</option>
                        <option value="quitado" {{ request('status') == 'quitado' ? 'selected' : '' }}>Quitado</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    @if(request()->anyFilled(['search', 'empresa_id', 'tipo_pessoa', 'status']))
                    <a href="{{ route('devedores.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">Limpar</a>
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
                            <th class="px-3 py-2">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CPF/CNPJ</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden lg:table-cell">Título ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($devedores as $devedor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-3 py-3">
                                <input type="checkbox" name="devedores[]" value="{{ $devedor->id }}" class="devedor-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $devedor->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                @if($devedor->tipo_pessoa == 'F')
                                    {{ $devedor->nome ?? '-' }}
                                @else
                                    @php
                                        $nomeFantasia = trim($devedor->nome_fantasia ?? '');
                                        $razaoSocial = trim($devedor->razao_social ?? '');
                                        $nomeEmpresa = $nomeFantasia ?: $razaoSocial;
                                    @endphp
                                    @if($nomeEmpresa)
                                        {{ $nomeEmpresa }}
                                    @else
                                        <span class="text-gray-400 italic">Sem nome cadastrado</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 font-mono">
                                @if($devedor->tipo_pessoa == 'F')
                                    {{ $devedor->cpf ?? '-' }}
                                @else
                                    {{ $devedor->cnpj ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden md:table-cell">{{ $devedor->empresa_nome ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden lg:table-cell">{{ $devedor->titulo_id_exemplo ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = 'Desconhecido';

                                    if ($devedor->status_baixa_num == 0) {
                                        $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                        $statusText = 'Pendente';
                                    } elseif ($devedor->status_baixa_num == 2) {
                                        $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                        $statusText = 'Quitado';
                                    } elseif ($devedor->status_baixa_num == 3) {
                                        $statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                        $statusText = 'Negociado';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($devedor->titulo_id_exemplo)
                                    <a href="#" onclick="verDetalhes({{ $devedor->titulo_id_exemplo }})"
                                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                       title="Visualizar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @endif

                                    <a href="{{ route('devedores.edit', $devedor->id) }}"
                                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('devedores.titulos', $devedor->id) }}"
                                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                       title="Ver Títulos">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>

                                    @if($devedor->status_baixa_num != 0)
                                    <a href="#" onclick="refazerDevedor({{ $devedor->id }}, '{{ addslashes($devedor->nome ?? $devedor->razao_social) }}')"
                                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                       title="Refazer (Voltar para Pendente)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </a>
                                    @endif

                                    <div class="relative">
                                        <button onclick="toggleDropdown({{ $devedor->id }})"
                                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Mais ações">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"></path>
                                            </svg>
                                        </button>

                                        <div id="dropdown-{{ $devedor->id }}" class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50 hidden">
                                            <a href="#" onclick="alterarOperador({{ $devedor->id }})"
                                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Alterar Operador
                                            </a>
                                            <a href="#" onclick="alterarConsultor({{ $devedor->id }})"
                                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Alterar Consultor
                                            </a>
                                            <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                            <a href="#" onclick="excluirDevedor({{ $devedor->id }}, '{{ addslashes($devedor->nome ?? $devedor->razao_social) }}')"
                                               class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Excluir Devedor
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum devedor encontrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Ações em Massa -->
            @if($devedores->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            <span id="selected-count">0</span> devedor(es) selecionado(s)
                        </span>
                        <div class="flex gap-2">
                            <button onclick="consultarAPI()" id="btn-consultar-api" disabled
                                    class="px-3 py-1 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                Consultar API
                            </button>
                            <button onclick="excluirSelecionados()" id="btn-excluir-massa" disabled
                                    class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                Excluir Selecionados
                            </button>
                        </div>
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Página {{ $devedores->currentPage() }} de {{ $devedores->lastPage() }}
                    </div>
                </div>
            </div>
            @endif

            @if($devedores->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $devedores->links() }}
            </div>
            @endif
        </div>

        <!-- JavaScript para funcionalidades -->
        <script>
            // Seleção múltipla
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.devedor-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedCount();
            });

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('devedor-checkbox')) {
                    updateSelectedCount();
                    document.getElementById('select-all').checked = false;
                }
            });

            function updateSelectedCount() {
                const selected = document.querySelectorAll('.devedor-checkbox:checked').length;
                document.getElementById('selected-count').textContent = selected;

                const btnConsultar = document.getElementById('btn-consultar-api');
                const btnExcluir = document.getElementById('btn-excluir-massa');

                btnConsultar.disabled = selected === 0;
                btnExcluir.disabled = selected === 0;
            }

            // Dropdown toggle
            function toggleDropdown(devedorId) {
                const dropdown = document.getElementById(`dropdown-${devedorId}`);
                const isHidden = dropdown.classList.contains('hidden');

                // Fechar todos os dropdowns
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });

                // Abrir/fechar o dropdown clicado
                if (isHidden) {
                    dropdown.classList.remove('hidden');
                }
            }

            // Fechar dropdowns ao clicar fora
            document.addEventListener('click', function(e) {
                if (!e.target.closest('[id^="dropdown-"]') && !e.target.closest('button[onclick*="toggleDropdown"]')) {
                    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                        d.classList.add('hidden');
                    });
                }
            });

            // Funções das ações
            function verDetalhes(tituloId) {
                window.open(`/detalhes-devedor/${tituloId}`, '_blank');
            }

            function refazerDevedor(devedorId, nome) {
                if (confirm(`Isso vai APAGAR a negociação/baixa e voltar TODOS os títulos de "${nome}" para PENDENTE. Continuar?`)) {
                    fetch(`/devedores/${devedorId}/refazer`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Erro ao refazer devedor');
                        console.error(error);
                    });
                }
            }

            function alterarOperador(devedorId) {
                // Implementar modal para alterar operador
                alert('Funcionalidade de alterar operador será implementada');
            }

            function alterarConsultor(devedorId) {
                // Implementar modal para alterar consultor
                alert('Funcionalidade de alterar consultor será implementada');
            }

            function excluirDevedor(devedorId, nome) {
                if (confirm(`Tem certeza que deseja excluir o devedor "${nome}"?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/devedores/${devedorId}`;

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    form.appendChild(methodField);
                    form.appendChild(csrfField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function consultarAPI() {
                const selected = document.querySelectorAll('.devedor-checkbox:checked');
                if (selected.length === 0) {
                    alert('Selecione pelo menos um devedor');
                    return;
                }

                const ids = Array.from(selected).map(cb => cb.value);
                alert(`Consultando API para ${ids.length} devedor(es)... (Funcionalidade será implementada)`);
            }

            function excluirSelecionados() {
                const selected = document.querySelectorAll('.devedor-checkbox:checked');
                if (selected.length === 0) {
                    alert('Selecione pelo menos um devedor');
                    return;
                }

                if (confirm(`Tem certeza que deseja excluir ${selected.length} devedor(es) selecionado(s)?`)) {
                    const ids = Array.from(selected).map(cb => cb.value);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/devedores/excluir-em-massa';

                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'devedores[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Funções do header (placeholders por enquanto)
            function baixarModelo() {
                alert('Funcionalidade de baixar modelo será implementada');
            }

            function abrirModalImportacao() {
                alert('Funcionalidade de importação será implementada');
            }
        </script>
    </div>
</x-app-layout>