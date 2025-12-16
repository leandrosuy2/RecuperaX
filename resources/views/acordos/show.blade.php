<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Acordo #{{ $acordo->id }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Devedor: {{ $acordo->nome_devedor }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('acordos.gerar-contrato', $acordo) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Gerar Contrato PDF
                </a>
                <a href="{{ route('acordos.index') }}" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    ← Voltar
                </a>
            </div>
        </div>

        <!-- Informações do Acordo -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Acordo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Devedor</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $acordo->nome_devedor }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Empresa</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $acordo->empresa ? ($acordo->empresa->nome_fantasia ?? $acordo->empresa->razao_social) : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Total Negociado</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($acordo->valor_total_negociacao, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Entrada</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($acordo->entrada, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Quantidade de Parcelas</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $acordo->qtde_prc }}x</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor por Parcela</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($acordo->valor_por_parcela, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Vencimento da Primeira Parcela</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $acordo->venc_primeira_parcela->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Contato</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $acordo->contato ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Parcelas -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Parcelas</h2>
            @if($acordo->parcelas->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Parcela</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vencimento</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Pagamento</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($acordo->parcelas as $parcela)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $parcela->parcela_numero }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($parcela->status === 'PAGO')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Pago</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pendente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $parcela->data_baixa ? $parcela->data_baixa->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($parcela->status === 'PENDENTE')
                                    <a href="{{ route('parcelamentos.pagar', $parcela) }}" class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors" title="Pagar parcela">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    <a href="{{ route('parcelamentos.show', $parcela) }}" class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors" title="Ver detalhes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma parcela cadastrada</p>
            @endif
        </div>
    </div>
</x-app-layout>
