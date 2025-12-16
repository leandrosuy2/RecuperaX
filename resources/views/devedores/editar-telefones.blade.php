<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Editar Telefones</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Devedor: {{ $devedor->nome_completo }}</p>
            </div>
            <a href="{{ route('devedores.show', $devedor) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <form method="POST" action="{{ route('devedores.atualizar-telefones', $devedor) }}" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Telefones</h2>
                <div class="space-y-4">
                    @for($i = 1; $i <= 10; $i++)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone {{ $i }}</label>
                            <input type="text" name="telefone{{ $i }}" value="{{ old("telefone{$i}", $devedor->{"telefone{$i}"}) }}" 
                                   class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="(00) 00000-0000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="telefone{{ $i }}_valido" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="NAO VERIFICADO" {{ ($devedor->{"telefone{$i}_valido"} ?? 'NAO VERIFICADO') === 'NAO VERIFICADO' ? 'selected' : '' }}>Não Verificado</option>
                                <option value="SIM" {{ ($devedor->{"telefone{$i}_valido"} ?? '') === 'SIM' ? 'selected' : '' }}>Válido</option>
                                <option value="NAO" {{ ($devedor->{"telefone{$i}_valido"} ?? '') === 'NAO' ? 'selected' : '' }}>Inválido</option>
                            </select>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('devedores.show', $devedor) }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Atualizar Telefones
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
