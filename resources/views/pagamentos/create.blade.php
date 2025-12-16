<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Novo Pagamento</h1>
                <p class="text-sm text-gray-600 mt-1">Registrar pagamento recebido</p>
            </div>
            <a href="{{ route('pagamentos.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>

        @if($divida || $acordo)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    @if($acordo)
                    <p class="text-sm font-medium text-green-900">Acordo selecionado</p>
                    <p class="text-xs text-green-700 mt-1">
                        <strong>{{ $acordo->numero_acordo }}</strong> - {{ $acordo->cliente->nome }} - 
                        Valor Acordado: R$ {{ number_format($acordo->valor_acordado, 2, ',', '.') }} - 
                        Restante: R$ {{ number_format($acordo->valor_restante, 2, ',', '.') }}
                    </p>
                    @elseif($divida)
                    <p class="text-sm font-medium text-green-900">Dívida selecionada</p>
                    <p class="text-xs text-green-700 mt-1">
                        <strong>{{ $divida->numero_documento }}</strong> - {{ $divida->cliente->nome }} - 
                        Valor: R$ {{ number_format($divida->valor_atual, 2, ',', '.') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Formulário -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('pagamentos.store') }}" method="POST">
                @csrf
                
                @if($acordo)
                <input type="hidden" name="acordo_id" value="{{ $acordo->id }}">
                <input type="hidden" name="divida_id" value="{{ $acordo->divida_id }}">
                @elseif($divida)
                <input type="hidden" name="divida_id" value="{{ $divida->id }}">
                @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dívida ou Acordo *</label>
                    <select name="divida_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione uma dívida</option>
                        @foreach(auth()->user()->isConsultor() ? auth()->user()->dividas()->get() : \App\Models\Divida::all() as $div)
                        <option value="{{ $div->id }}" {{ old('divida_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->numero_documento }} - {{ $div->cliente->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                        <input type="number" step="0.01" name="valor" value="{{ old('valor', $acordo->valor_parcela ?? '') }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data do Pagamento *</label>
                        <input type="date" name="data_pagamento" value="{{ old('data_pagamento', now()->format('Y-m-d')) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento *</label>
                        <select name="forma_pagamento" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pix" {{ old('forma_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="boleto" {{ old('forma_pagamento') == 'boleto' ? 'selected' : '' }}>Boleto</option>
                            <option value="transferencia" {{ old('forma_pagamento') == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                            <option value="dinheiro" {{ old('forma_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="cartao_credito" {{ old('forma_pagamento') == 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                            <option value="cartao_debito" {{ old('forma_pagamento') == 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                            <option value="cheque" {{ old('forma_pagamento') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>

                    @if($acordo)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número da Parcela</label>
                        <input type="number" min="1" name="numero_parcela" value="{{ old('numero_parcela') }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('observacoes') }}</textarea>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Registrar Pagamento
                    </button>
                    <a href="{{ route('pagamentos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
