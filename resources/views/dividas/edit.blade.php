<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Dívida</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $divida->numero_documento }}</p>
            </div>
            <a href="{{ route('dividas.show', $divida) }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>

        <!-- Formulário -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('dividas.update', $divida) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Credor *</label>
                        <select name="credor_id" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($credores as $credor)
                            <option value="{{ $credor->id }}" {{ old('credor_id', $divida->credor_id) == $credor->id ? 'selected' : '' }}>
                                {{ $credor->razao_social }}
                            </option>
                            @endforeach
                        </select>
                        @error('credor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                        <select name="cliente_id" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $divida->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nome }}
                            </option>
                            @endforeach
                        </select>
                        @error('cliente_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nº Documento *</label>
                        <input type="text" name="numero_documento" value="{{ old('numero_documento', $divida->numero_documento) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('numero_documento')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consultor</label>
                        <select name="consultor_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Não atribuído</option>
                            @foreach($consultores as $consultor)
                            <option value="{{ $consultor->id }}" {{ old('consultor_id', $divida->consultor_id) == $consultor->id ? 'selected' : '' }}>
                                {{ $consultor->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor Original *</label>
                        <input type="number" step="0.01" name="valor_original" value="{{ old('valor_original', $divida->valor_original) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('valor_original')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor Atual *</label>
                        <input type="number" step="0.01" name="valor_atual" value="{{ old('valor_atual', $divida->valor_atual) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('valor_atual')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Emissão *</label>
                        <input type="date" name="data_emissao" value="{{ old('data_emissao', $divida->data_emissao->format('Y-m-d')) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                        <input type="date" name="data_vencimento" value="{{ old('data_vencimento', $divida->data_vencimento->format('Y-m-d')) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="a_vencer" {{ old('status', $divida->status) == 'a_vencer' ? 'selected' : '' }}>A vencer</option>
                            <option value="vencida" {{ old('status', $divida->status) == 'vencida' ? 'selected' : '' }}>Vencida</option>
                            <option value="em_negociacao" {{ old('status', $divida->status) == 'em_negociacao' ? 'selected' : '' }}>Em negociação</option>
                            <option value="quitada" {{ old('status', $divida->status) == 'quitada' ? 'selected' : '' }}>Quitada</option>
                            <option value="cancelada" {{ old('status', $divida->status) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <input type="text" name="descricao" value="{{ old('descricao', $divida->descricao) }}" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('dividas.show', $divida) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

