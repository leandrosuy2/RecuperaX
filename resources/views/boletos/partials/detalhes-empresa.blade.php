<div class="space-y-6">
    <!-- Dados da Empresa -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dados da Empresa</h4>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Razão Social</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->razao_social }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">CNPJ</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ substr($empresa->cnpj, 0, 2) }}.{{ substr($empresa->cnpj, 2, 3) }}.{{ substr($empresa->cnpj, 5, 3) }}/{{ substr($empresa->cnpj, 8, 4) }}-{{ substr($empresa->cnpj, 12, 2) }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->telefone ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Endereço</h4>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $empresa->endereco ?? '-' }}
                        @if($empresa->numero), {{ $empresa->numero }}@endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Bairro</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->bairro ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade/UF</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $empresa->cidade ?? '-' }}/{{ $empresa->uf ?? '-' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">CEP</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->cep ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo da Comissão -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Resumo da Comissão</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Recebido</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    R$ {{ number_format($dadosEmpresa->valor_recebido_total ?? 0, 2, ',', '.') }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Comissão</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    R$ {{ number_format($dadosEmpresa->comissao_total ?? 0, 2, ',', '.') }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Maior Atraso</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                    {{ $dadosEmpresa->dias_max_emp_hist ?? 0 }} dias
                </p>
            </div>
        </div>
    </div>

    <!-- Títulos Baixados -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Títulos Baixados na Janela</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nº Título</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vencimento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data Baixa</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($titulos as $titulo)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->id }}</td>
                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->nome_devedor }}</td>
                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->num_titulo }}</td>
                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                            R$ {{ number_format($titulo->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->vencimento }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($titulo->status_txt === 'Quitado') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($titulo->status_txt === 'Negociado') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ $titulo->status_txt }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->data_baixa_fmt }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Nenhum título encontrado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>