<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Follow-ups de {{ $devedor->nome_completo }}</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Histórico de contatos</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('followups.create', ['devedor_id' => $devedor->id]) }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    + Novo Follow-up
                </a>
                <a href="{{ route('devedores.show', $devedor) }}" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    ← Voltar ao Devedor
                </a>
            </div>
        </div>

        <!-- Lista de Follow-ups -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
            @if($followups->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($followups as $followup)
                <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $followup->created_at->format('d/m/Y H:i') }}</p>
                                @if($followup->empresa)
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    {{ $followup->empresa->nome_fantasia ?? $followup->empresa->razao_social }}
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $followup->texto }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('followups.show', $followup) }}" 
                               class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                               title="Ver detalhes">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('followups.destroy', $followup) }}" method="POST" class="inline" onsubmit="return confirm('Deseja excluir este follow-up?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-colors"
                                        title="Excluir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($followups->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $followups->links() }}
            </div>
            @endif
            @else
            <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                Nenhum follow-up registrado para este devedor.
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
