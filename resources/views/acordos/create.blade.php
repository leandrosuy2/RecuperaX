<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Realizar Acordo</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Criar acordo de negociação</p>
            </div>
            <a href="{{ $titulo ? route('titulos.show', $titulo) : route('acordos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        @if($titulo)
        <!-- Informações do Título -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Título</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Devedor</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->devedor->nome_completo }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Original</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Vencimento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $titulo->dataVencimento->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor com Juros (estimado)</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($titulo->valor_com_juros, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('acordos.store') }}" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            @if($titulo)
                <input type="hidden" name="titulo_id" value="{{ $titulo->id }}">
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selecionar Título *</label>
                    <select name="titulo_id" id="titulo_id" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione um título...</option>
                        @foreach($titulos ?? [] as $t)
                        <option value="{{ $t->id }}">
                            #{{ $t->num_titulo }} - {{ $t->devedor->nome_completo }} - R$ {{ number_format($t->valor, 2, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('titulo_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            @endif

            <!-- Entrada -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Entrada</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor da Entrada *</label>
                        <input type="number" name="entrada" step="0.01" min="0" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('entrada')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Entrada *</label>
                        <input type="date" name="data_entrada" value="{{ date('Y-m-d') }}" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('data_entrada')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Forma de Pagamento</label>
                        <select name="forma_pag_Id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione...</option>
                            <option value="1">PIX</option>
                            <option value="2">Boleto</option>
                            <option value="3">Dinheiro</option>
                            <option value="4">Cartão de Crédito</option>
                            <option value="5">Cartão de Débito</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contato</label>
                        <input type="text" name="contato" placeholder="Telefone ou email usado" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Parcelas -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Parcelas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade de Parcelas *</label>
                        <input type="number" name="qtde_prc" id="qtde_prc" min="1" max="24" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('qtde_prc')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor por Parcela *</label>
                        <input type="number" name="valor_por_parcela" step="0.01" min="0" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('valor_por_parcela')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vencimento da Primeira Parcela *</label>
                        <input type="date" name="venc_primeira_parcela" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('venc_primeira_parcela')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex gap-3 justify-end">
                <a href="{{ $titulo ? route('titulos.show', $titulo) : route('acordos.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Criar Acordo
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
