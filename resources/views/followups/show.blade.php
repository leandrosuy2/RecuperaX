<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Follow-up #{{ $followup->id }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registrado em {{ $followup->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('followups.destroy', $followup) }}" method="POST" class="inline" onsubmit="return confirm('Deseja excluir este follow-up?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Excluir
                    </button>
                </form>
                <a href="{{ route('followups.index') }}" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    ← Voltar
                </a>
            </div>
        </div>

        <!-- Informações do Follow-up -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Follow-up</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Devedor</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        <a href="{{ route('devedores.show', $followup->devedor) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                            {{ $followup->devedor->nome_completo }}
                        </a>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Empresa</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $followup->empresa ? ($followup->empresa->nome_fantasia ?? $followup->empresa->razao_social) : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Registro</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $followup->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Texto do Follow-up -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Texto do Follow-up</h2>
            <div class="prose dark:prose-invert max-w-none">
                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $followup->texto }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
