<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Emitir Boletos</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                    Comissão semanal: {{ $sextaRef->format('d/m/Y') }} - {{ $proximaSexta->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <!-- Navegação entre semanas -->
                <a href="{{ route('emitir-boletos', ['from' => $sextaRef->copy()->subDays(7)->format('Y-m-d')]) }}"
                   class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Semana Anterior
                </a>
                <a href="{{ route('emitir-boletos', ['from' => $proximaSexta->format('Y-m-d')]) }}"
                   class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium px-3 py-2 rounded-lg transition-colors">
                    Semana Próxima
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Dashboard de Totais -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Empresas Elegíveis</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $empresasComComissao->count() }}</p>
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
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Valor Recebido</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                            R$ {{ number_format($empresasComComissao->sum('valor_recebido'), 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-800 dark:text-purple-200">Comissão Total</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                            R$ {{ number_format($empresasComComissao->sum('comissao_total'), 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-purple-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabela de Empresas -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Razão Social</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">CNPJ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dias Atraso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Comissão</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="empresas-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($empresasComComissao as $empresa)
                        <tr id="empresa-row-{{ $empresa->empresa_id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $empresa->razao_social }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $empresa->telefone ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 hidden md:table-cell">
                                {{ substr($empresa->cnpj, 0, 2) }}.{{ substr($empresa->cnpj, 2, 3) }}.{{ substr($empresa->cnpj, 5, 3) }}/{{ substr($empresa->cnpj, 8, 4) }}-{{ substr($empresa->cnpj, 12, 2) }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                @php
                                    $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                    if ($empresa->dias_max >= 1826) {
                                        $badgeClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                    } elseif ($empresa->dias_max >= 721) {
                                        $badgeClass = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                                    } elseif ($empresa->dias_max >= 181) {
                                        $badgeClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $empresa->dias_max }} dias
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                R$ {{ number_format($empresa->valor_recebido, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-semibold text-indigo-600 dark:text-indigo-400">
                                R$ {{ number_format($empresa->comissao_total, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Detalhes -->
                                    <button onclick="mostrarDetalhes({{ $empresa->empresa_id }}, '{{ addslashes($empresa->razao_social) }}')"
                                            class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                            title="Ver Detalhes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>

                                    <!-- Emitir Boleto -->
                                    <button onclick="emitirBoleto({{ $empresa->empresa_id }}, {{ $empresa->comissao_total }}, '{{ addslashes($empresa->razao_social) }}')"
                                            class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                            title="Emitir Boleto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </button>

                                    <!-- Gerar Cobrança -->
                                    <button onclick="gerarCobranca({{ $empresa->empresa_id }}, {{ $empresa->comissao_total }}, '{{ addslashes($empresa->razao_social) }}')"
                                            class="p-2 text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900 rounded-lg transition-colors"
                                            title="Gerar Cobrança">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>

                                    <!-- Cobrar via WhatsApp -->
                                    <button onclick="cobrarViaWhatsapp({{ $empresa->empresa_id }}, {{ $empresa->comissao_total }}, '{{ addslashes($empresa->razao_social) }}', '{{ $empresa->whatsapp_financeiro ?? '' }}')"
                                            class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                            title="Cobrar via WhatsApp">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </button>

                                    <!-- Dar Baixa -->
                                    <button onclick="darBaixaEmpresa({{ $empresa->empresa_id }}, {{ $empresa->comissao_total }}, '{{ addslashes($empresa->razao_social) }}')"
                                            class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                            title="Dar Baixa (Marcar como Pago)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>

                                    <!-- Excluir -->
                                    <button onclick="excluirEmpresa({{ $empresa->empresa_id }}, '{{ addslashes($empresa->razao_social) }}')"
                                            class="p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-colors"
                                            title="Excluir da Lista">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma empresa elegível encontrada para esta semana
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cobranças Geradas -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Cobranças Geradas</h2>
            </div>

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar Cobranças</label>
                        <input type="text" id="search-cobrancas" placeholder="Digite empresa, CNPJ, ID ou valor..."
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pesquise por nome da empresa, CNPJ, ID da cobrança ou valor</p>
                    </div>
                </div>
            </div>

            <!-- Tabela de Cobranças -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Cobrança</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Comissão</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Link/Documento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status Pagamento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Criado em</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="cobrancas-table">
                            @forelse($cobrancas as $cobranca)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $cobranca->pago ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $cobranca->id }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <div>
                                        <div class="font-medium">{{ $cobranca->empresa->razao_social ?? 'Empresa não encontrada' }}</div>
                                        <div class="text-gray-500 dark:text-gray-400 text-xs">
                                            CNPJ: {{ $cobranca->empresa ? substr($cobranca->empresa->cnpj, 0, 2) . '.' . substr($cobranca->empresa->cnpj, 2, 3) . '.' . substr($cobranca->empresa->cnpj, 5, 3) . '/' . substr($cobranca->empresa->cnpj, 8, 4) . '-' . substr($cobranca->empresa->cnpj, 12, 2) : 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($cobranca->data_cobranca)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                    R$ {{ number_format($cobranca->valor_comissao, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $cobranca->tipo_anexo === 'documento' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' }}">
                                        {{ ucfirst($cobranca->tipo_anexo) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($cobranca->tipo_anexo === 'link' && $cobranca->link)
                                        <a href="{{ $cobranca->link }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            Abrir Link
                                        </a>
                                    @elseif($cobranca->tipo_anexo === 'documento' && $cobranca->documento)
                                        <a href="{{ Storage::url($cobranca->documento) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Baixar Documento
                                        </a>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox"
                                               onchange="atualizarStatusCobranca({{ $cobranca->id }}, this.checked)"
                                               {{ $cobranca->pago ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm {{ $cobranca->pago ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $cobranca->pago ? 'Pago' : 'Não Pago' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $cobranca->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Nenhuma cobrança encontrada
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Mostrando as últimas {{ $cobrancas->count() }} cobranças geradas
            </p>
        </div>

        <!-- Modal de Detalhes da Empresa -->
        <div id="modal-detalhes" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detalhes da Empresa</h3>
                    <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modal-content">
                    <!-- Conteúdo será carregado dinamicamente -->
                </div>
            </div>
        </div>

        <!-- Modal de Emitir Boleto -->
        <div id="modal-boleto" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Emitir Boleto</h3>
                    <button onclick="fecharModalBoleto()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="form-boleto" onsubmit="processarEmissao(event)">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                            <input type="text" id="boleto-empresa-nome" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                            <input type="hidden" id="boleto-empresa-id" name="empresa_id">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor da Comissão</label>
                            <input type="text" id="boleto-valor" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Vencimento</label>
                            <input type="date" name="data_vencimento" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2" min="{{ now()->addDay()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="fecharModalBoleto()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">
                            Emitir Boleto
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Gerar Cobrança -->
        <div id="modal-cobranca" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Gerar Cobrança</h3>
                    <button onclick="fecharModalCobranca()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="form-cobranca" onsubmit="processarCobranca(event)">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                            <input type="text" id="cobranca-empresa-nome" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                            <input type="hidden" id="cobranca-empresa-id" name="empresa_id">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Cobrança</label>
                            <input type="date" name="data_cobranca" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor da Comissão</label>
                            <input type="text" id="cobranca-valor" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Anexo</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="tipo_anexo" value="documento" required class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Documento (PDF, DOC, etc.)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="tipo_anexo" value="link" required class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Link do documento</span>
                                </label>
                            </div>
                        </div>
                        <div id="campo-documento" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Arquivo</label>
                            <input type="file" name="documento" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        </div>
                        <div id="campo-link" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL do Documento</label>
                            <input type="url" name="link" placeholder="https://..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="fecharModalCobranca()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">
                            Gerar Cobrança
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Cobrar via WhatsApp -->
        <div id="modal-cobrar-whatsapp" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Cobrar via WhatsApp</h3>
                    <button onclick="fecharModalCobrarWhatsapp()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="form-cobrar-whatsapp" onsubmit="enviarCobrancaWhatsapp(event)">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                            <input type="text" id="cobrar-empresa-nome" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                            <input type="hidden" id="cobrar-empresa-id" name="empresa_id">
                            <input type="hidden" id="cobrar-valor-comissao" name="valor_comissao">
                            <input type="hidden" id="cobrar-reference-id" name="reference_id">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número do WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" id="cobrar-whatsapp-numero" name="numero_whatsapp"
                                   placeholder="5511999999999" required
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato: código do país + DDD + número (ex: 5511999999999)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link de Pagamento</label>
                            <input type="url" id="cobrar-link-pagamento" name="link_pagamento"
                                   placeholder="https://..." 
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Link do boleto ou sistema de pagamento</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mensagem</label>
                            <textarea id="cobrar-mensagem" name="mensagem" rows="10"
                                      class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="fecharModalCobrarWhatsapp()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-enviar-cobranca" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">
                            Enviar Mensagem
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Confirmação Dar Baixa -->
        <div id="modal-confirmar-baixa" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" onclick="if(event.target.id === 'modal-confirmar-baixa') fecharModalConfirmarBaixa()">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Confirmar Baixa</h3>
                    <button onclick="fecharModalConfirmarBaixa()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                        <input type="text" id="confirmar-baixa-empresa-nome" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor da Comissão</label>
                        <input type="text" id="confirmar-baixa-valor" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Atenção:</strong> Ao confirmar, a empresa será marcada como paga e removida da lista de emissão de boletos.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="fecharModalConfirmarBaixa()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmarBaixa()" id="btn-confirmar-baixa" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">
                        Confirmar Baixa
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Envio WhatsApp -->
        <div id="modal-whatsapp" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Enviar Boleto via WhatsApp</h3>
                    <button onclick="fecharModalWhatsapp()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="whatsapp-content">
                    <!-- Conteúdo será carregado dinamicamente -->
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script>
            // Controle dos campos de anexo
            document.querySelectorAll('input[name="tipo_anexo"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('campo-documento').classList.toggle('hidden', this.value !== 'documento');
                    document.getElementById('campo-link').classList.toggle('hidden', this.value !== 'link');
                });
            });

            // Funções dos modais
            function mostrarDetalhes(empresaId, empresaNome) {
                // Carregar detalhes via AJAX
                const url = `/emitir-boletos/detalhes-empresa/${empresaId}?from={{ $sextaRef->format('Y-m-d') }}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modal-content').innerHTML = data.html;
                        document.getElementById('modal-detalhes').classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao carregar detalhes da empresa');
                    });
            }

            function fecharModal() {
                document.getElementById('modal-detalhes').classList.add('hidden');
            }

            function emitirBoleto(empresaId, valor, empresaNome) {
                document.getElementById('boleto-empresa-id').value = empresaId;
                document.getElementById('boleto-empresa-nome').value = empresaNome;
                document.getElementById('boleto-valor').value = 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('modal-boleto').classList.remove('hidden');
            }

            function fecharModalBoleto() {
                document.getElementById('modal-boleto').classList.add('hidden');
            }

            function gerarCobranca(empresaId, valor, empresaNome) {
                document.getElementById('cobranca-empresa-id').value = empresaId;
                document.getElementById('cobranca-empresa-nome').value = empresaNome;
                document.getElementById('cobranca-valor').value = 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('modal-cobranca').classList.remove('hidden');
            }

            function fecharModalCobranca() {
                document.getElementById('modal-cobranca').classList.add('hidden');
            }

            function mostrarModalWhatsapp(boletoId) {
                // Carregar conteúdo do WhatsApp via AJAX
                fetch(`/boletos/${boletoId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const boleto = data.boleto;
                            const empresa = data.empresa;

                            let html = `
                                <div class="space-y-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Dados do Boleto</h4>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div><strong>Empresa:</strong> ${empresa.razao_social}</div>
                                            <div><strong>Valor:</strong> R$ ${parseFloat(boleto.valor_nominal).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
                                            <div><strong>Vencimento:</strong> ${new Date(boleto.data_vencimento).toLocaleDateString('pt-BR')}</div>
                                            <div><strong>Código:</strong> ${boleto.codigo_solicitacao}</div>
                                        </div>
                                    </div>

                                    ${boleto.pix_copia_e_cola ? `
                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                        <h4 class="font-medium text-green-800 dark:text-green-200 mb-2">PIX Copia-e-Cola</h4>
                                        <p class="text-sm text-green-700 dark:text-green-300 break-all">${boleto.pix_copia_e_cola}</p>
                                        <button onclick="copiarParaAreaTransferencia('${boleto.pix_copia_e_cola}')" class="mt-2 text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                                            Copiar código PIX
                                        </button>
                                    </div>
                                    ` : ''}

                                    ${boleto.linha_digitavel ? `
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                        <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Linha Digitável</h4>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 font-mono">${boleto.linha_digitavel}</p>
                                        <button onclick="copiarParaAreaTransferencia('${boleto.linha_digitavel}')" class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            Copiar linha digitável
                                        </button>
                                    </div>
                                    ` : ''}

                                    <form id="form-whatsapp" onsubmit="enviarWhatsapp(event, ${boletoId})">
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número do WhatsApp</label>
                                                <input type="text" id="whatsapp-numero" name="numero_whatsapp"
                                                       placeholder="(11) 99999-9999" required
                                                       class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mensagem</label>
                                                <textarea id="whatsapp-mensagem" name="mensagem" rows="8"
                                                          class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">${gerarMensagemPadrao(empresa, boleto)}</textarea>
                                            </div>

                                            <div class="flex items-center">
                                                <input type="checkbox" id="atualizar-telefone" name="atualizar_telefone"
                                                       class="text-indigo-600 focus:ring-indigo-500">
                                                <label for="atualizar-telefone" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                    Atualizar telefone da empresa
                                                </label>
                                            </div>
                                        </div>

                                        <div class="flex gap-3 justify-end mt-6">
                                            <button type="button" onclick="fecharModalWhatsapp()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">
                                                Enviar WhatsApp
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            `;

                            document.getElementById('whatsapp-content').innerHTML = html;
                            $dispatch('open-modal', 'whatsapp');
                        } else {
                            alert('Erro ao carregar dados do boleto');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao carregar dados do boleto');
                    });
            }


            function gerarMensagemPadrao(empresa, boleto) {
                let mensagem = `Olá, ${empresa.razao_social}\n\n`;
                mensagem += `Segue relatório e boleto referente aos honorários da semana.\n\n`;

                if (boleto.pix_copia_e_cola) {
                    mensagem += `Você pode pagar via *Pix Copia-e-Cola* colando o código abaixo no app do banco.\n\n`;
                    mensagem += `${boleto.pix_copia_e_cola}\n\n`;
                }

                if (boleto.linha_digitavel) {
                    mensagem += `Se preferir, pague pelo boleto (linha digitável abaixo).\n\n`;
                    mensagem += `Linha digitável: ${boleto.linha_digitavel}\n`;
                }

                mensagem += `\nAtenciosamente\n`;
                mensagem += `Francisco Bordin`;

                return mensagem;
            }

            function copiarParaAreaTransferencia(texto) {
                navigator.clipboard.writeText(texto).then(() => {
                    // Mostrar feedback visual
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    notification.textContent = 'Copiado!';
                    document.body.appendChild(notification);
                    setTimeout(() => document.body.removeChild(notification), 2000);
                });
            }

            function enviarWhatsapp(event, boletoId) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                fetch(`/boletos/${boletoId}/enviar-whatsapp`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Abrir WhatsApp em nova aba
                        window.open(data.whatsapp_url, '_blank');
                        $dispatch('close-modal', 'whatsapp');
                        alert('Mensagem preparada! WhatsApp foi aberto em nova aba.');
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar WhatsApp');
                });
            }

            function excluirEmpresa(empresaId, empresaNome) {
                if (confirm(`Tem certeza que deseja excluir "${empresaNome}" da lista de emissão?`)) {
                    // Implementar exclusão (remover da visualização atual)
                    alert('Funcionalidade de exclusão será implementada');
                }
            }

            // Variáveis globais para o modal de confirmação
            let empresaBaixaId = null;
            let empresaBaixaValor = null;
            let empresaBaixaNome = null;

            function darBaixaEmpresa(empresaId, valorComissao, empresaNome) {
                // Armazenar dados para usar no confirmar
                empresaBaixaId = empresaId;
                empresaBaixaValor = valorComissao;
                empresaBaixaNome = empresaNome;

                // Preencher o modal
                document.getElementById('confirmar-baixa-empresa-nome').value = empresaNome;
                document.getElementById('confirmar-baixa-valor').value = 'R$ ' + parseFloat(valorComissao).toLocaleString('pt-BR', {minimumFractionDigits: 2});

                // Abrir modal
                document.getElementById('modal-confirmar-baixa').classList.remove('hidden');
            }

            function fecharModalConfirmarBaixa() {
                document.getElementById('modal-confirmar-baixa').classList.add('hidden');
                empresaBaixaId = null;
                empresaBaixaValor = null;
                empresaBaixaNome = null;
            }

            function confirmarBaixa() {
                if (!empresaBaixaId) {
                    return;
                }

                // Encontrar a linha da tabela pelo ID
                const row = document.getElementById('empresa-row-' + empresaBaixaId);

                // Desabilitar botão durante o processamento
                const btnConfirmar = document.getElementById('btn-confirmar-baixa');
                btnConfirmar.disabled = true;
                btnConfirmar.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processando...';

                fetch('/boletos/dar-baixa-empresa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        empresa_id: empresaBaixaId,
                        valor_comissao: empresaBaixaValor
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Fechar modal
                        fecharModalConfirmarBaixa();

                        // Remover a linha da tabela com animação
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                // Verificar se não há mais empresas na tabela
                                const tbody = document.getElementById('empresas-table-body');
                                if (tbody && tbody.children.length === 0) {
                                    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma empresa encontrada.</td></tr>';
                                }
                            }, 300);
                        }

                        // Mostrar mensagem de sucesso
                        alert('✓ ' + data.message);
                    } else {
                        btnConfirmar.disabled = false;
                        btnConfirmar.textContent = 'Confirmar Baixa';
                        alert('✗ ' + (data.message || 'Erro ao dar baixa'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    btnConfirmar.disabled = false;
                    btnConfirmar.textContent = 'Confirmar Baixa';
                    alert('✗ Erro ao dar baixa');
                });
            }

            function cobrarViaWhatsapp(empresaId, valorComissao, empresaNome, telefone) {
                document.getElementById('cobrar-empresa-id').value = empresaId;
                document.getElementById('cobrar-empresa-nome').value = empresaNome;
                document.getElementById('cobrar-valor-comissao').value = valorComissao;
                
                // Limpar campos
                document.getElementById('cobrar-link-pagamento').value = '';
                document.getElementById('cobrar-mensagem').value = '';
                document.getElementById('cobrar-reference-id').value = '';
                
                // Preencher telefone se disponível
                if (telefone) {
                    // Limpar e formatar telefone
                    const telefoneLimpo = telefone.replace(/\D/g, '');
                    if (telefoneLimpo.length >= 10) {
                        // Se não começa com 55, adicionar
                        const telefoneFormatado = telefoneLimpo.startsWith('55') ? telefoneLimpo : '55' + telefoneLimpo;
                        document.getElementById('cobrar-whatsapp-numero').value = telefoneFormatado;
                    }
                }

                // Mostrar modal com loading
                document.getElementById('modal-cobrar-whatsapp').classList.remove('hidden');
                const button = document.getElementById('btn-enviar-cobranca');
                button.disabled = true;
                button.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Gerando link...';

                // Gerar link de pagamento PicPay
                fetch('/boletos/gerar-link-picpay', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        empresa_id: empresaId,
                        valor: valorComissao,
                        empresa_nome: empresaNome
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Erro ao gerar link de pagamento');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    button.disabled = false;
                    button.innerHTML = 'Enviar Mensagem';
                    
                    if (data.success && data.payment_url) {
                        document.getElementById('cobrar-link-pagamento').value = data.payment_url;
                        
                        // Salvar reference_id se disponível
                        if (data.reference_id) {
                            document.getElementById('cobrar-reference-id').value = data.reference_id;
                        }
                        
                        // Montar mensagem com link
                        const dataInicio = '{{ $sextaRef->format('d/m/Y') }}';
                        const dataFim = '{{ $proximaSexta->copy()->subDay()->format('d/m/Y') }}';
                        const valorFormatado = parseFloat(valorComissao).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        const linkPagamento = data.payment_url;
                        
                        let mensagem = `Olá, segue link de acesso ao título referente aos nossos honorários, esses referente ao período de ${dataInicio} até ${dataFim}.\n\n`;
                        mensagem += `O relatório do mesmo encontra-se disponível no seu acesso ao escritório on-line\n\n`;
                        mensagem += `${linkPagamento}\n\n`;
                        mensagem += `Valor da comissão: R$ ${valorFormatado}\n\n`;
                        mensagem += `Pedimos que ao efetuar o pagamento nos encaminhe o comprovante.\n\n`;
                        mensagem += `Att:.\n`;
                        mensagem += `Financeiro BRD intermediações`;
                        
                        document.getElementById('cobrar-mensagem').value = mensagem;
                    } else {
                        const errorMsg = data.message || 'Erro ao gerar link de pagamento. Verifique as configurações do PicPay.';
                        alert('⚠️ ' + errorMsg + '\n\nVocê pode adicionar o link manualmente no campo "Link de Pagamento".');
                        
                        // Montar mensagem sem link
                        const dataInicio = '{{ $sextaRef->format('d/m/Y') }}';
                        const dataFim = '{{ $proximaSexta->copy()->subDay()->format('d/m/Y') }}';
                        const valorFormatado = parseFloat(valorComissao).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        
                        let mensagem = `Olá, segue link de acesso ao título referente aos nossos honorários, esses referente ao período de ${dataInicio} até ${dataFim}.\n\n`;
                        mensagem += `O relatório do mesmo encontra-se disponível no seu acesso ao escritório on-line\n\n`;
                        mensagem += `Valor da comissão: R$ ${valorFormatado}\n\n`;
                        mensagem += `Pedimos que ao efetuar o pagamento nos encaminhe o comprovante.\n\n`;
                        mensagem += `Att:.\n`;
                        mensagem += `Financeiro BRD intermediações`;
                        
                        document.getElementById('cobrar-mensagem').value = mensagem;
                    }
                })
                .catch(error => {
                    console.error('Erro ao gerar link:', error);
                    button.disabled = false;
                    button.innerHTML = 'Enviar Mensagem';
                    
                    const errorMsg = error.message || 'Erro ao gerar link de pagamento. Verifique as configurações do PicPay nas configurações de pagamento.';
                    alert('⚠️ ' + errorMsg + '\n\nVocê pode adicionar o link manualmente no campo "Link de Pagamento".');
                    
                    // Montar mensagem sem link
                    const dataInicio = '{{ $sextaRef->format('d/m/Y') }}';
                    const dataFim = '{{ $proximaSexta->copy()->subDay()->format('d/m/Y') }}';
                    const valorFormatado = parseFloat(valorComissao).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    
                    let mensagem = `Olá, segue link de acesso ao título referente aos nossos honorários, esses referente ao período de ${dataInicio} até ${dataFim}.\n\n`;
                    mensagem += `O relatório do mesmo encontra-se disponível no seu acesso ao escritório on-line\n\n`;
                    mensagem += `Valor da comissão: R$ ${valorFormatado}\n\n`;
                    mensagem += `Pedimos que ao efetuar o pagamento nos encaminhe o comprovante.\n\n`;
                    mensagem += `Att:.\n`;
                    mensagem += `Financeiro BRD intermediações`;
                    
                    document.getElementById('cobrar-mensagem').value = mensagem;
                });
            }

            function fecharModalCobrarWhatsapp() {
                document.getElementById('modal-cobrar-whatsapp').classList.add('hidden');
            }

            function enviarCobrancaWhatsapp(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);
                
                const button = document.getElementById('btn-enviar-cobranca');
                button.disabled = true;
                button.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Enviando...';

                fetch('/boletos/cobrar-via-whatsapp', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✓ ' + data.message);
                        fecharModalCobrarWhatsapp();
                    } else {
                        alert('✗ ' + (data.message || 'Erro ao enviar mensagem'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('✗ Erro ao enviar mensagem.');
                })
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = 'Enviar Mensagem';
                });
            }

            // Processar emissão de boleto
            function processarEmissao(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                fetch('/emitir-boletos', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Boleto emitido com sucesso!');
                        fecharModalBoleto();

                        // Mostrar modal do WhatsApp se o boleto foi criado
                        if (data.boleto && data.boleto.id) {
                            mostrarModalWhatsapp(data.boleto.id);
                        } else {
                            location.reload();
                        }
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao emitir boleto');
                });
            }

            // Processar geração de cobrança
            function processarCobranca(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                fetch('/gerar-cobranca', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cobrança gerada com sucesso!');
                        fecharModalCobranca();
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao gerar cobrança');
                });
            }

            // Atualizar status de pagamento da cobrança
            function atualizarStatusCobranca(cobrancaId, pago) {
                fetch(`/cobrancas/${cobrancaId}/atualizar-pago`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ pago: pago })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar visualmente a linha
                        const row = event.target.closest('tr');
                        if (pago) {
                            row.classList.add('bg-green-50', 'dark:bg-green-900/20');
                            event.target.nextElementSibling.textContent = 'Pago';
                            event.target.nextElementSibling.className = 'ml-2 text-sm text-green-600 dark:text-green-400 font-medium';
                        } else {
                            row.classList.remove('bg-green-50', 'dark:bg-green-900/20');
                            event.target.nextElementSibling.textContent = 'Não Pago';
                            event.target.nextElementSibling.className = 'ml-2 text-sm text-gray-700 dark:text-gray-300';
                        }
                    } else {
                        alert('Erro ao atualizar status: ' + (data.message || 'Erro desconhecido'));
                        // Reverter o checkbox
                        event.target.checked = !pago;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao atualizar status');
                    // Reverter o checkbox
                    event.target.checked = !pago;
                });
            }

            // Funcionalidade de busca nas cobranças
            document.getElementById('search-cobrancas')?.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#cobrancas-table tr');

                rows.forEach(row => {
                    if (row.cells.length > 1) { // Ignorar linha vazia
                        const empresa = row.cells[1]?.textContent.toLowerCase() || '';
                        const id = row.cells[0]?.textContent.toLowerCase() || '';
                        const valor = row.cells[3]?.textContent.toLowerCase() || '';

                        if (empresa.includes(searchTerm) || id.includes(searchTerm) || valor.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        </script>
    </div>
</x-app-layout>