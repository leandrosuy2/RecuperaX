<x-app-layout>
    <div class="space-y-6">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detalhes do Devedor</h1>
                <div class="flex items-center gap-3 mt-2">
                    @if($operadorAtual)
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                            Operador: {{ $operadorAtual }}
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            Sem operador
                        </span>
                    @endif
                    @if($consultorAtual)
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            Consultor: {{ $consultorAtual }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="abrirModalOperador()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-yellow-200 dark:bg-yellow-800 hover:bg-yellow-300 dark:hover:bg-yellow-700 rounded-lg transition-colors">
                    Trocar Operador
                </button>
                <button onclick="abrirModalConsultor()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 rounded-lg transition-colors">
                    Trocar Consultor
                </button>
                <a href="{{ route('devedores.edit', $devedor->id) }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Editar
                </a>
                <a href="{{ route('devedores.index') }}" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    ← Voltar
                </a>
            </div>
        </div>

        <!-- Alerta de Óbito -->
        @if($devedor->cpf)
        <div class="p-4 rounded-lg {{ $obitoInfo ? 'bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700' : 'bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700' }}">
            <p class="text-sm font-medium {{ $obitoInfo ? 'text-red-800 dark:text-red-200' : 'text-green-800 dark:text-green-200' }}">
                @if($obitoInfo)
                    ⚠️ CPF consta como falecido. Data: {{ $obitoInfo['data'] ?? 'N/A' }} - Fonte: {{ $obitoInfo['fonte'] ?? 'N/A' }}
                @else
                    ✓ Sem registro de óbito para este CPF.
                @endif
            </p>
        </div>
        @endif

        <!-- Informações do Devedor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Devedor</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->nome_completo ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CPF:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', preg_replace('/\D/', '', $devedor->cpf)) : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CNPJ:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->cnpj ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome da Mãe:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->nome_mae ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">RG:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->rg ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Observação:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->observacao ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CEP:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->cep ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Endereço:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->endereco ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bairro:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->bairro ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cidade:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->cidade ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">UF:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->uf ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">E-mail 1:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->email1 ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">E-mail 2:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->email2 ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Telefones -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Telefones</h2>
            
            <!-- Legenda -->
            <div class="mb-4 flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-green-600">✔</span>
                    <span class="text-gray-600 dark:text-gray-400">Telefone válido</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-red-600">✖</span>
                    <span class="text-gray-600 dark:text-gray-400">Telefone inválido</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-yellow-600">?</span>
                    <span class="text-gray-600 dark:text-gray-400">Telefone não verificado</span>
                </div>
            </div>
            
            <form action="{{ route('devedores.atualizar-telefones', $devedor->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @for($i = 1; $i <= 10; $i++)
                        @php 
                            $telefone = $devedor->{"telefone{$i}"};
                            $valido = $devedor->{"telefone{$i}_valido"} ?? 'NAO VERIFICADO';
                        @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Telefone {{ $i }}</label>
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       name="telefone{{ $i }}" 
                                       value="{{ $telefone }}" 
                                       placeholder="(00) 00000-0000"
                                       class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 {{ $valido === 'SIM' ? 'bg-green-50 dark:bg-green-900/20' : ($valido === 'NAO' ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:bg-gray-700') }}">
                                <input type="hidden" name="telefone{{ $i }}_valido" id="telefone{{ $i }}_valido" value="{{ $valido }}">
                                <div class="flex gap-1">
                                    <button type="button" 
                                            onclick="atualizarStatusTelefone({{ $i }}, 'SIM')"
                                            class="p-2 {{ $valido === 'SIM' ? 'text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-lg transition-colors"
                                            title="Válido">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            onclick="atualizarStatusTelefone({{ $i }}, 'NAO')"
                                            class="p-2 {{ $valido === 'NAO' ? 'text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-lg transition-colors"
                                            title="Inválido">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            onclick="atualizarStatusTelefone({{ $i }}, 'NAO VERIFICADO')"
                                            class="p-2 {{ $valido === 'NAO VERIFICADO' ? 'text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-lg transition-colors"
                                            title="Não Verificado">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    @if($telefone)
                                    <button type="button" 
                                            onclick="abrirWhatsApp('{{ preg_replace('/\D/', '', $telefone) }}')"
                                            class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                            title="Abrir WhatsApp">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Salvar Telefones
                    </button>
                </div>
            </form>
        </div>

        <!-- Gerar proposta ao credor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Gerar proposta ao credor</h2>
            
            <form id="propostaForm" class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Base (pendente + juros)</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="baseComJuros">R$ {{ number_format($baseComJuros ?? 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Capital (sem juros)</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="capital">R$ {{ number_format($capital ?? 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Juros apurados</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="jurosApurados">R$ {{ number_format($jurosApurados ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desconto à vista (%) apenas nos juros</label>
                            <button type="button" onclick="document.getElementById('descontoVistaJuros').value = 45; calcularProposta();" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                usar 45%
                            </button>
                        </div>
                        <input type="number" id="descontoVistaJuros" value="45" min="0" max="100" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desconto parcelado (%) apenas nos juros</label>
                            <button type="button" onclick="document.getElementById('descontoParceladoJuros').value = 55; calcularProposta();" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                usar 55%
                            </button>
                        </div>
                        <input type="number" id="descontoParceladoJuros" value="55" min="0" max="100" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entrada (%)</label>
                        <input type="number" id="entradaPercent" value="30" min="0" max="100" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade de parcelas</label>
                        <input type="number" id="qtdeParcelas" value="3" min="1" max="24" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da entrada</label>
                        <input type="date" id="dataEntrada" value="{{ date('Y-m-d') }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">À vista (capital + juros c/ desconto)</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="totalVista">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total parcelado (capital + juros c/ desconto)</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="totalParcelado">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Entrada (auto)</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="entradaAuto">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Parcela (auto)</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="parcelaAuto">R$ 0,00</span>
                    </div>
                </div>
                
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="permitirEdicao" class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Permitir edição manual dos campos calculados</span>
                    </label>
                </div>
                
                <!-- Cronograma das parcelas -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Cronograma das parcelas (auto)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Vencimento</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Valor</th>
                                </tr>
                            </thead>
                            <tbody id="cronogramaParcelas" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Prévia do texto ao CREDOR -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Prévia do texto ao CREDOR (será salvo em Detalhamento)</h3>
                        <div class="flex gap-2">
                            <button type="button" onclick="copiarTextoCredor()" class="px-3 py-1 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg transition-colors">
                                Copiar texto
                            </button>
                            <button type="button" onclick="salvarEmDetalhamento()" class="px-3 py-1 text-xs font-medium text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 bg-green-50 dark:bg-green-900/20 rounded-lg transition-colors">
                                Salvar em Detalhamento
                            </button>
                            <button type="button" onclick="enviarPropostaWhatsApp()" class="px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                Enviar WhatsApp
                            </button>
                        </div>
                    </div>
                    <textarea id="textoCredor" rows="15" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2" readonly></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Observação: o texto acima é apenas para o CREDOR. Não há envio por WhatsApp desta proposta.
                    </p>
                </div>
            </form>
        </div>

        <!-- Títulos Associados -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Títulos Associados</h2>
                <a href="{{ route('devedores.adicionar-titulo', $devedor->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    + Adicionar Título
                </a>
            </div>
            
            <!-- Tabela: Títulos de Entrada -->
            @if($titulosEntrada->count() > 0)
            <div class="mb-6">
                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">Títulos de Entrada</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Título</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Divida</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Forma Pagamento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data baixa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vencimento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Juros</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor com juros</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dias Atraso</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($titulosEntrada as $titulo)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->empresa ? ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $devedor->nome_completo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->num_titulo ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valorRecebido ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->forma_pag_Id !== null ? ($formaPagamentoMap[$titulo->forma_pag_Id] ?? 'Não informado') : 'Não informado' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->data_baixa ? $titulo->data_baixa->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($titulo->statusBaixa === 0 || $titulo->statusBaixa === null)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pendente</span>
                                    @elseif($titulo->statusBaixa === 2)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Quitado</span>
                                    @elseif($titulo->statusBaixa === 3)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Negociado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->juros ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor_com_juros ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->dias_atraso ?? ($titulo->dataVencimento ? max(0, now()->diffInDays($titulo->dataVencimento)) : 0) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($titulo->operador)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">{{ $titulo->operador }}</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Sem operador</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($titulo->statusBaixa === 0 || $titulo->statusBaixa === null)
                                            <a href="{{ route('acordos.create', ['titulo_id' => $titulo->id]) }}" 
                                               class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                               title="Gerar Acordo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="abrirModalBaixar({{ $titulo->id }}, {{ $titulo->valor }}, '{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('Y-m-d') : '' }}')"
                                                    class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                                    title="Baixar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('titulos.edit', $titulo->id) }}" 
                                               class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                               title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @elseif($titulo->statusBaixa === 3)
                                            <button onclick="abrirModalQuitarEntrada({{ $titulo->id }}, {{ $titulo->valor }}, '{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('Y-m-d') : '' }}')"
                                                    class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                                    title="Quitar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('titulos.edit', $titulo->id) }}" 
                                               class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                               title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @elseif($titulo->statusBaixa === 2)
                                            <a href="{{ route('titulos.gerar-recibo', $titulo->id) }}" 
                                               class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                               title="Gerar Recibo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Tabela: Títulos Associados (Parcelas) -->
            @if($titulosAssociados->count() > 0)
            <div>
                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">Títulos Associados (Parcelas)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Devedor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Título</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Recebido</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total & Parcela</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Forma de Pagamento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vencimento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data de Baixa</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Juros</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dias de atraso</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($titulosAssociados as $titulo)
                            @php
                                $isVencidoNegociado = ($titulo->statusBaixa === 3 && $titulo->dataVencimento && $titulo->dataVencimento < now());
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isVencidoNegociado ? 'bg-orange-50 dark:bg-orange-900/20' : '' }}">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->empresa ? ($titulo->empresa->nome_fantasia ?? $titulo->empresa->razao_social) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $devedor->nome_completo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->num_titulo ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valorRecebido ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->forma_pag_Id !== null ? ($formaPagamentoMap[$titulo->forma_pag_Id] ?? '-') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $titulo->data_baixa ? $titulo->data_baixa->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->juros ?? 0, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $titulo->dias_atraso ?? ($titulo->dataVencimento ? max(0, now()->diffInDays($titulo->dataVencimento)) : 0) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($titulo->statusBaixa === 0 || $titulo->statusBaixa === null)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pendente</span>
                                    @elseif($titulo->statusBaixa === 2)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Quitado</span>
                                    @elseif($titulo->statusBaixa === 3)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Negociado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($titulo->operador)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">{{ $titulo->operador }}</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Sem operador</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($titulo->statusBaixa === 3)
                                            <button onclick="abrirModalQuitarParcela({{ $titulo->id }}, {{ $titulo->valor }}, '{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('Y-m-d') : '' }}')"
                                                    class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                                    title="Quitar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('titulos.edit', $titulo->id) }}" 
                                               class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                               title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @elseif($titulo->statusBaixa === 2)
                                            <a href="{{ route('titulos.gerar-recibo', $titulo->id) }}" 
                                               class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                               title="Gerar Recibo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                        @elseif($titulo->statusBaixa === 0 || $titulo->statusBaixa === null)
                                            <a href="{{ route('acordos.create', ['titulo_id' => $titulo->id]) }}" 
                                               class="p-2 text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900 rounded-lg transition-colors"
                                               title="Gerar Acordo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="abrirModalBaixar({{ $titulo->id }}, {{ $titulo->valor }}, '{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('Y-m-d') : '' }}')"
                                                    class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors"
                                                    title="Baixar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('titulos.edit', $titulo->id) }}" 
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            
            @if($titulosEntrada->count() == 0 && $titulosAssociados->count() == 0)
            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum título cadastrado</p>
            @endif
        </div>

        <!-- Empresa Credora -->
        @if($devedor->empresa)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Empresa Credora</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Razão Social:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->razao_social ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome Fantasia:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->nome_fantasia ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CNPJ:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->cnpj ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Telefone:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->telefone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">E-mail:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Endereço:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->endereco ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bairro:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->bairro ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">UF:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->uf ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cidade:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->cidade ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Chave pix:</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->empresa->banco ?? ($devedor->empresa->nome_favorecido_pix ?? '-') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Agendamentos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Agendamentos</h2>
                <a href="{{ route('agendamentos.create', ['devedor_id' => $devedor->id]) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    + Criar Novo Agendamento
                </a>
            </div>
            @if($devedor->agendamentos->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data de Abertura</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data de Retorno</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Assunto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operador</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Telefone</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($devedor->agendamentos as $agendamento)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->data_abertura ? $agendamento->data_abertura->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->data_retorno ? $agendamento->data_retorno->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($agendamento->assunto, 50) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->operador ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $agendamento->status === 'Finalizado' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                    {{ $agendamento->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                @if($agendamento->telefone)
                                    <div class="flex items-center gap-2">
                                        <span>{{ $agendamento->telefone }}</span>
                                        <button onclick="abrirWhatsApp('{{ preg_replace('/\D/', '', $agendamento->telefone) }}')" 
                                                class="p-1 bg-green-500 hover:bg-green-600 text-white rounded transition-colors"
                                                title="Abrir WhatsApp">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum agendamento registrado</p>
            @endif
        </div>

        <!-- Detalhamento -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detalhamento</h2>
            
            <form action="{{ route('followups.adicionar', $devedor->id) }}" method="POST" class="mb-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Novo Detalhamento:</label>
                    <textarea name="texto" rows="3" placeholder="Digite aqui o histórico do WhatsApp ou informações relevantes." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2"></textarea>
                </div>
                <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Salvar Detalhamento
                </button>
            </form>
            
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">Histórico</h3>
            @if($devedor->followUps->count() > 0)
            <div class="space-y-3">
                @foreach($devedor->followUps as $followup)
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $followup->texto }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $followup->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum Follow-up registrado.</p>
            @endif
        </div>
    </div>

    <!-- Modais -->
    
    <!-- Modal: Trocar Operador -->
    <div id="modalOperador" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Trocar Operador</h3>
            <form id="formTrocarOperador" onsubmit="trocarOperador(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operador</label>
                    <select name="operador" id="selectOperador" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        <option value="">Selecione...</option>
                        @foreach($operadores as $operador)
                        <option value="{{ $operador->name }}" {{ $operadorAtual === $operador->name ? 'selected' : '' }}>{{ $operador->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="fecharModalOperador()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg transition-colors">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Trocar Consultor -->
    <div id="modalConsultor" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Trocar Consultor</h3>
            <form id="formTrocarConsultor" onsubmit="trocarConsultor(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Consultor</label>
                    <select name="consultor" id="selectConsultor" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        <option value="">Selecione...</option>
                        @foreach($operadores as $operador)
                        <option value="{{ $operador->name }}" {{ $consultorAtual === $operador->name ? 'selected' : '' }}>{{ $operador->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="fecharModalConsultor()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Quitar Entrada/Parcela -->
    <div id="modalQuitar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quitar Título</h3>
            <form id="formQuitar" method="POST">
                @csrf
                <input type="hidden" name="titulo_id" id="quitarTituloId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
                        <input type="text" id="quitarNumTitulo" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor a Quitar</label>
                        <input type="text" id="quitarValorQuitar" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vencimento</label>
                        <input type="text" id="quitarVencimento" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Devedor</label>
                        <input type="text" value="{{ $devedor->nome_completo }}" readonly class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor Recebido *</label>
                        <input type="number" name="valor_recebido" id="quitarValorRecebido" step="0.01" min="0" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 hidden" id="erroValor">Valor recebido não pode ser menor que o valor do título.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Baixa *</label>
                        <input type="date" name="data_baixa" id="quitarDataBaixa" value="{{ date('Y-m-d') }}" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Forma de Pagamento *</label>
                        <select name="forma_pagamento" id="quitarFormaPagamento" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                            <option value="0">Pix</option>
                            <option value="1">Dinheiro</option>
                            <option value="2">Cartão de Débito</option>
                            <option value="3">Cartão de Crédito</option>
                            <option value="4">Cheque</option>
                            <option value="5">Depósito em Conta</option>
                            <option value="6">Pagamento na Loja</option>
                            <option value="7">Boleto Bancário</option>
                            <option value="8">Duplicata</option>
                            <option value="9">Recebimento pelo credor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comprovante</label>
                        <input type="file" name="comprovante" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PDF ou imagem, máx. 10MB</p>
                    </div>
                </div>
                <div class="flex gap-2 justify-end mt-6">
                    <button type="button" onclick="fecharModalQuitar()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        Quitar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: WhatsApp -->
    <div id="modalWhatsApp" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Enviar WhatsApp</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selecionar Número</label>
                    <select id="selectTelefoneWhatsApp" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                        <option value="">Selecione um telefone...</option>
                        @for($i = 1; $i <= 10; $i++)
                            @php $telefone = $devedor->{"telefone{$i}"}; @endphp
                            @if($telefone)
                            <option value="{{ preg_replace('/\D/', '', $telefone) }}">{{ $telefone }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selecionar Template</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="escolherMsg('padrao')" id="btnPadrao" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-indigo-600 text-white">
                            Padrão
                        </button>
                        <button type="button" onclick="escolherMsg('vencidas')" id="btnVencidas" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            Vencidas
                        </button>
                        <button type="button" onclick="escolherMsg('a_vencer')" id="btnAVencer" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            A vencer
                        </button>
                        <button type="button" onclick="escolherMsg('quebra')" id="btnQuebra" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            Quebra de acordo
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preview da Mensagem</label>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 max-h-64 overflow-y-auto">
                        <pre id="previewMensagem" class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap"></pre>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="fecharModalWhatsApp()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="enviarWhats()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                        Abrir WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const baseComJuros = Math.max(0, {{ $baseComJuros ?? 0 }});
        const capital = Math.max(0, {{ $capital ?? 0 }});
        const jurosApurados = Math.max(0, {{ $jurosApurados ?? 0 }});
        const primeiroVencimento = '{{ $primeiroVencimento ? $primeiroVencimento->format('d/m/Y') : '' }}';
        const diasAtrasoMax = Math.max(0, {{ $diasAtrasoMax ?? 0 }});
        const nomeDevedor = '{{ addslashes($devedor->nome_completo) }}';
        const nomeEmpresa = '{{ addslashes($devedor->empresa ? ($devedor->empresa->nome_fantasia ?? $devedor->empresa->razao_social) : '') }}';
        const cpfCnpjMascarado = '{{ $devedor->cpf ? substr(preg_replace('/\D/', '', $devedor->cpf), 0, 6) . 'xxx.xx' : ($devedor->cnpj ? substr(preg_replace('/\D/', '', $devedor->cnpj), 0, 8) + 'xxx/xxxx-xx' : '') }}';
        const devedorId = {{ $devedor->id }};
        let tipoMensagemAtual = 'padrao';
        let mensagemAtual = '{{ addslashes($msgPadrao) }}';

        // Funções de Modal
        function abrirModalOperador() {
            document.getElementById('modalOperador').classList.remove('hidden');
        }

        function fecharModalOperador() {
            document.getElementById('modalOperador').classList.add('hidden');
        }

        function abrirModalConsultor() {
            document.getElementById('modalConsultor').classList.remove('hidden');
        }

        function fecharModalConsultor() {
            document.getElementById('modalConsultor').classList.add('hidden');
        }

        function abrirModalBaixar(tituloId, valor, vencimento) {
            document.getElementById('quitarTituloId').value = tituloId;
            document.getElementById('quitarNumTitulo').value = 'Título #' + tituloId;
            document.getElementById('quitarValorQuitar').value = 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('quitarValorRecebido').value = valor;
            document.getElementById('quitarVencimento').value = vencimento ? new Date(vencimento).toLocaleDateString('pt-BR') : '-';
            document.getElementById('formQuitar').action = '/titulos/' + tituloId + '/baixar';
            document.getElementById('modalQuitar').classList.remove('hidden');
        }

        function abrirModalQuitarEntrada(tituloId, valor, vencimento) {
            abrirModalBaixar(tituloId, valor, vencimento);
        }

        function abrirModalQuitarParcela(tituloId, valor, vencimento) {
            document.getElementById('quitarTituloId').value = tituloId;
            document.getElementById('quitarNumTitulo').value = 'Título #' + tituloId;
            document.getElementById('quitarValorQuitar').value = 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('quitarValorRecebido').value = valor;
            document.getElementById('quitarVencimento').value = vencimento ? new Date(vencimento).toLocaleDateString('pt-BR') : '-';
            document.getElementById('formQuitar').action = '/titulos/' + tituloId + '/quitar-parcela';
            document.getElementById('modalQuitar').classList.remove('hidden');
            
            // Validação adicional para parcela
            document.getElementById('quitarValorRecebido').addEventListener('input', function() {
                const valorRecebido = parseFloat(this.value) || 0;
                if (valorRecebido < valor) {
                    document.getElementById('erroValor').classList.remove('hidden');
                } else {
                    document.getElementById('erroValor').classList.add('hidden');
                }
            });
        }

        function fecharModalQuitar() {
            document.getElementById('modalQuitar').classList.add('hidden');
            document.getElementById('erroValor').classList.add('hidden');
        }

        function abrirWhatsApp(numero) {
            abrirModalWhatsApp();
            document.getElementById('selectTelefoneWhatsApp').value = numero;
            atualizarPreviewMensagem();
        }

        function abrirModalWhatsApp() {
            // Mostrar todos os botões de template
            document.querySelectorAll('[id^="btn"]').forEach(btn => {
                btn.style.display = '';
            });
            
            // Se não for proposta, resetar para padrão
            if (tipoMensagemAtual !== 'proposta') {
                escolherMsg('padrao');
            }
            
            document.getElementById('modalWhatsApp').classList.remove('hidden');
            atualizarPreviewMensagem();
        }

        function fecharModalWhatsApp() {
            document.getElementById('modalWhatsApp').classList.add('hidden');
        }

        function escolherMsg(tipo) {
            tipoMensagemAtual = tipo;
            
            // Atualizar botões
            ['padrao', 'vencidas', 'a_vencer', 'quebra'].forEach(t => {
                const btn = document.getElementById('btn' + t.charAt(0).toUpperCase() + t.slice(1).replace('_', ''));
                if (btn) {
                    if (t === tipo) {
                        btn.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
                        btn.classList.add('bg-indigo-600', 'text-white');
                    } else {
                        btn.classList.remove('bg-indigo-600', 'text-white');
                        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
                    }
                }
            });

            // Atualizar mensagem
            if (tipo === 'proposta') {
                mensagemAtual = document.getElementById('textoCredor').value;
            } else {
                const mensagens = {
                    'padrao': '{{ addslashes($msgPadrao) }}',
                    'vencidas': '{{ addslashes($msgVencidas) }}',
                    'a_vencer': '{{ addslashes($msgAVencer) }}',
                    'quebra': '{{ addslashes($msgQuebra) }}'
                };
                mensagemAtual = mensagens[tipo] || mensagens['padrao'];
            }
            atualizarPreviewMensagem();
        }

        function atualizarPreviewMensagem() {
            let msg = mensagemAtual;
            // Apenas substituir variáveis se não for proposta (que já está formatada)
            if (tipoMensagemAtual !== 'proposta') {
                msg = msg.replace(/%Nome%/g, nomeDevedor);
                msg = msg.replace(/%CpfCnpjMascarado%/g, cpfCnpjMascarado);
                msg = msg.replace(/%NomeCredor%/g, nomeEmpresa);
            }
            document.getElementById('previewMensagem').textContent = msg;
        }

        function enviarWhats() {
            const numero = document.getElementById('selectTelefoneWhatsApp').value;
            if (!numero) {
                alert('Selecione um número de telefone');
                return;
            }

            // Preparar mensagem final
            let mensagemFinal = mensagemAtual;
            if (tipoMensagemAtual !== 'proposta') {
                mensagemFinal = mensagemAtual.replace(/%Nome%/g, nomeDevedor).replace(/%CpfCnpjMascarado%/g, cpfCnpjMascarado).replace(/%NomeCredor%/g, nomeEmpresa);
            }

            // Salvar follow-up automaticamente
            const textoFollowup = `[WHATSAPP • ${tipoMensagemAtual.toUpperCase()}] (Operador: {{ auth()->user()->name }})\nPara: ${document.getElementById('selectTelefoneWhatsApp').selectedOptions[0].text}\n\n${mensagemFinal}`;
            
            const formData = new FormData();
            formData.append('texto', textoFollowup);
            @if($devedor->empresa_id)
            formData.append('empresa_id', {{ $devedor->empresa_id }});
            @endif
            formData.append('_token', '{{ csrf_token() }}');
            
            navigator.sendBeacon('/adicionar-follow-up/{{ $devedor->id }}', formData);

            // Abrir WhatsApp
            const numeroFormatado = '55' + numero;
            const mensagemEncoded = encodeURIComponent(mensagemFinal);
            window.open(`https://wa.me/${numeroFormatado}?text=${mensagemEncoded}`, '_blank');
            
            fecharModalWhatsApp();
        }

        function atualizarStatusTelefone(num, status) {
            document.getElementById('telefone' + num + '_valido').value = status;
            const input = document.querySelector(`input[name="telefone${num}"]`);
            const btnSim = input.parentElement.querySelector('button[onclick*="SIM"]');
            const btnNao = input.parentElement.querySelector('button[onclick*="NAO"]');
            const btnNaoVerificado = input.parentElement.querySelector('button[onclick*="NAO VERIFICADO"]');
            
            // Remover classes ativas de todos os botões
            [btnSim, btnNao, btnNaoVerificado].forEach(btn => {
                if (btn) {
                    btn.className = btn.className.replace(/text-(green|red|yellow)-600 dark:text-(green|red|yellow)-400 hover:text-(green|red|yellow)-900 dark:hover:text-(green|red|yellow)-300 hover:bg-(green|red|yellow)-50 dark:hover:bg-(green|red|yellow)-900/g, '');
                    btn.className += ' text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700';
                }
            });
            
            // Aplicar classes ativas ao botão selecionado
            if (status === 'SIM') {
                input.classList.remove('bg-red-50', 'dark:bg-red-900/20', 'bg-yellow-50', 'dark:bg-yellow-900/20', 'bg-gray-50', 'dark:bg-gray-700');
                input.classList.add('bg-green-50', 'dark:bg-green-900/20');
                if (btnSim) {
                    btnSim.className = btnSim.className.replace(/text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700/g, '');
                    btnSim.className += ' text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900';
                }
            } else if (status === 'NAO') {
                input.classList.remove('bg-green-50', 'dark:bg-green-900/20', 'bg-yellow-50', 'dark:bg-yellow-900/20', 'bg-gray-50', 'dark:bg-gray-700');
                input.classList.add('bg-red-50', 'dark:bg-red-900/20');
                if (btnNao) {
                    btnNao.className = btnNao.className.replace(/text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700/g, '');
                    btnNao.className += ' text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900';
                }
            } else {
                input.classList.remove('bg-green-50', 'dark:bg-green-900/20', 'bg-red-50', 'dark:bg-red-900/20', 'bg-gray-50', 'dark:bg-gray-700');
                input.classList.add('bg-yellow-50', 'dark:bg-yellow-900/20');
                if (btnNaoVerificado) {
                    btnNaoVerificado.className = btnNaoVerificado.className.replace(/text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700/g, '');
                    btnNaoVerificado.className += ' text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900';
                }
            }
        }

        function trocarOperador(event) {
            event.preventDefault();
            const operador = document.getElementById('selectOperador').value;
            
            fetch('/devedores/{{ $devedor->id }}/alterar-operador', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ operador: operador })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao alterar operador');
                }
            });
        }

        function trocarConsultor(event) {
            event.preventDefault();
            const consultor = document.getElementById('selectConsultor').value;
            
            fetch('/devedores/{{ $devedor->id }}/alterar-consultor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ consultor: consultor })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao alterar consultor');
                }
            });
        }

        function calcularProposta() {
            const descontoVistaJuros = Math.max(0, parseFloat(document.getElementById('descontoVistaJuros').value) || 0);
            const descontoParceladoJuros = Math.max(0, parseFloat(document.getElementById('descontoParceladoJuros').value) || 0);
            const entradaPercent = Math.max(0, parseFloat(document.getElementById('entradaPercent').value) || 0);
            const qtdeParcelas = Math.max(1, parseInt(document.getElementById('qtdeParcelas').value) || 1);
            const dataEntrada = new Date(document.getElementById('dataEntrada').value);

            // Garantir valores positivos
            const capitalPositivo = Math.max(0, capital);
            const jurosPositivo = Math.max(0, jurosApurados);

            // Calcular totais
            const jurosComDescontoVista = jurosPositivo * (1 - descontoVistaJuros / 100);
            const totalVista = capitalPositivo + Math.max(0, jurosComDescontoVista);
            
            const jurosComDescontoParcelado = jurosPositivo * (1 - descontoParceladoJuros / 100);
            const totalParcelado = capitalPositivo + Math.max(0, jurosComDescontoParcelado);
            
            const entrada = Math.max(0, totalParcelado * (entradaPercent / 100));
            const valorParcela = Math.max(0, (totalParcelado - entrada) / qtdeParcelas);

            // Atualizar valores
            document.getElementById('totalVista').textContent = 'R$ ' + totalVista.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('totalParcelado').textContent = 'R$ ' + totalParcelado.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('entradaAuto').textContent = 'R$ ' + entrada.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('parcelaAuto').textContent = 'R$ ' + valorParcela.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Gerar cronograma
            const tbody = document.getElementById('cronogramaParcelas');
            tbody.innerHTML = '';
            for (let i = 1; i <= qtdeParcelas; i++) {
                const dataVenc = new Date(dataEntrada);
                dataVenc.setMonth(dataVenc.getMonth() + i);
                const row = tbody.insertRow();
                row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                
                const cell1 = row.insertCell(0);
                cell1.className = 'px-4 py-3 text-sm text-gray-900 dark:text-gray-100';
                cell1.textContent = i;
                
                const cell2 = row.insertCell(1);
                cell2.className = 'px-4 py-3 text-sm text-gray-900 dark:text-gray-100';
                cell2.textContent = dataVenc.toLocaleDateString('pt-BR');
                
                const cell3 = row.insertCell(2);
                cell3.className = 'px-4 py-3 text-sm text-gray-900 dark:text-gray-100';
                cell3.textContent = 'R$ ' + valorParcela.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Gerar texto ao credor
            const baseComJurosPositivo = Math.max(0, baseComJuros);
            const texto = `Prezados,

Apresentamos a seguinte proposta de pagamento:
Devedor: ${nomeDevedor}

Empresa: ${nomeEmpresa}
Base com juros (pendente + juros): R$ ${baseComJurosPositivo.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
Capital (sem juros): R$ ${capitalPositivo.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
Juros apurados: R$ ${jurosPositivo.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
Primeiro vencimento do(s) título(s): ${primeiroVencimento}
Dias de atraso (máx.): ${Math.max(0, diasAtrasoMax)}
Desconto à vista (${descontoVistaJuros}% sobre juros) → Total à vista: R$ ${totalVista.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
Desconto parcelado (${descontoParceladoJuros}% sobre juros) → Total parcelado: R$ ${totalParcelado.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
Entrada em ${dataEntrada.toLocaleDateString('pt-BR')}: R$ ${entrada.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
Parcelas: ${qtdeParcelas}x de R$ ${valorParcela.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')}

Cronograma:
${Array.from({length: qtdeParcelas}, (_, i) => {
    const dataVenc = new Date(dataEntrada);
    dataVenc.setMonth(dataVenc.getMonth() + i + 1);
    return `Parcela ${i + 1}: R$ ${valorParcela.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.')} (venc. ${dataVenc.toLocaleDateString('pt-BR')})`;
}).join('\n')}

Atenciosamente,
{{ auth()->user()->name }}
Atendimento ao Cliente
Negociar Cobranças`;

            document.getElementById('textoCredor').value = texto;
        }

        // Adicionar listeners
        ['descontoVistaJuros', 'descontoParceladoJuros', 'entradaPercent', 'qtdeParcelas', 'dataEntrada'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', calcularProposta);
            }
        });

        // Calcular inicialmente
        calcularProposta();

        function copiarTextoCredor() {
            const texto = document.getElementById('textoCredor').value;
            navigator.clipboard.writeText(texto).then(() => {
                alert('Texto copiado para área de transferência!');
            });
        }

        function salvarEmDetalhamento() {
            const texto = document.getElementById('textoCredor').value;
            if (!texto.trim()) {
                alert('Não há texto para salvar');
                return;
            }

            const formData = new FormData();
            formData.append('texto', texto);
            @if($devedor->empresa_id)
            formData.append('empresa_id', {{ $devedor->empresa_id }});
            @endif
            formData.append('_token', '{{ csrf_token() }}');

            fetch('/adicionar-follow-up/{{ $devedor->id }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    alert('Texto salvo em Detalhamento com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao salvar texto');
                }
            });
        }

        function enviarPropostaWhatsApp() {
            const texto = document.getElementById('textoCredor').value;
            if (!texto.trim()) {
                alert('Não há proposta para enviar');
                return;
            }

            // Abrir modal WhatsApp com o texto da proposta
            mensagemAtual = texto;
            tipoMensagemAtual = 'proposta';
            
            // Ocultar botões de template quando for proposta
            document.querySelectorAll('[id^="btn"]').forEach(btn => {
                if (btn.id !== 'btnPadrao') {
                    btn.style.display = 'none';
                }
            });
            
            abrirModalWhatsApp();
            atualizarPreviewMensagem();
        }

        // Fechar modais ao clicar fora
        document.getElementById('modalOperador')?.addEventListener('click', function(e) {
            if (e.target === this) fecharModalOperador();
        });
        document.getElementById('modalConsultor')?.addEventListener('click', function(e) {
            if (e.target === this) fecharModalConsultor();
        });
        document.getElementById('modalQuitar')?.addEventListener('click', function(e) {
            if (e.target === this) fecharModalQuitar();
        });
        document.getElementById('modalWhatsApp')?.addEventListener('click', function(e) {
            if (e.target === this) fecharModalWhatsApp();
        });
    </script>
    @endpush
</x-app-layout>
