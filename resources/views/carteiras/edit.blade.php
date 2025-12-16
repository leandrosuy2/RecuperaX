<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Carteira</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $carteira->nome }}</p>
            </div>
            <a href="{{ route('carteiras.show', $carteira) }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>

        <!-- Formulário -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('carteiras.update', $carteira) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome', $carteira->nome) }}" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Consultor *</label>
                            <select name="consultor_id" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($consultores as $consultor)
                                <option value="{{ $consultor->id }}" {{ old('consultor_id', $carteira->consultor_id) == $consultor->id ? 'selected' : '' }}>
                                    {{ $consultor->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('consultor_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Credor</label>
                            <select name="credor_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                @foreach($credores as $credor)
                                <option value="{{ $credor->id }}" {{ old('credor_id', $carteira->credor_id) == $credor->id ? 'selected' : '' }}>
                                    {{ $credor->razao_social }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dias de Atraso Mínimo</label>
                            <input type="number" name="dias_atraso_min" value="{{ old('dias_atraso_min', $carteira->dias_atraso_min) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dias de Atraso Máximo</label>
                            <input type="number" name="dias_atraso_max" value="{{ old('dias_atraso_max', $carteira->dias_atraso_max) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Mínimo</label>
                            <input type="number" step="0.01" name="valor_min" value="{{ old('valor_min', $carteira->valor_min) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Máximo</label>
                            <input type="number" step="0.01" name="valor_max" value="{{ old('valor_max', $carteira->valor_max) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status_filtro" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            <option value="a_vencer" {{ old('status_filtro', $carteira->status_filtro) == 'a_vencer' ? 'selected' : '' }}>A vencer</option>
                            <option value="vencida" {{ old('status_filtro', $carteira->status_filtro) == 'vencida' ? 'selected' : '' }}>Vencida</option>
                            <option value="em_negociacao" {{ old('status_filtro', $carteira->status_filtro) == 'em_negociacao' ? 'selected' : '' }}>Em negociação</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea name="descricao" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('descricao', $carteira->descricao) }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="ativo" value="1" {{ old('ativo', $carteira->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">Carteira ativa</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('carteiras.show', $carteira) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
