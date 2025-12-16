<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Relatório por Credor</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Selecione um credor para visualizar o relatório</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($credores as $credor)
            <a href="{{ route('relatorios.credor', ['credor_id' => $credor->id]) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-all border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $credor->razao_social }}</h3>
                @if($credor->nome_fantasia)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $credor->nome_fantasia }}</p>
                @endif
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">CNPJ:</span>
                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $credor->cnpj }}</span>
                </div>
            </a>
            @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Nenhum credor encontrado</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
