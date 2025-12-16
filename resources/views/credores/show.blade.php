<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $credor->razao_social }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $credor->nome_fantasia }}</p>
            </div>
            @if(auth()->user()->canViewAllDividas())
            <a href="{{ route('credores.edit', $credor) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                Editar
            </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informações -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">CNPJ</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $credor->cnpj }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $credor->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</dt>
                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $credor->telefone ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estatísticas</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Dívidas</dt>
                        <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_dividas'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dívidas Ativas</dt>
                        <dd class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['dividas_ativas'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor em Atraso</dt>
                        <dd class="text-2xl font-bold text-red-600 dark:text-red-400">R$ {{ number_format($stats['valor_em_atraso'], 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Recuperado</dt>
                        <dd class="text-2xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($stats['valor_recuperado'], 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Dívidas Recentes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow transition-colors">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Dívidas Recentes</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($credor->dividas as $divida)
                <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $divida->numero_documento }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $divida->cliente->nome }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($divida->valor_atual, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $divida->data_vencimento->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">Nenhuma dívida cadastrada</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

