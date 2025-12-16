<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Relatório - {{ $consultor->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Estatísticas e desempenho do consultor</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 transition-colors">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="consultor_id" value="{{ $consultor->id }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Período Início</label>
                    <input type="date" name="periodo_inicio" value="{{ $periodoInicio }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Período Fim</label>
                    <input type="date" name="periodo_fim" value="{{ $periodoFim }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg w-full">Filtrar</button>
                </div>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Dívidas</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stats['total_dividas'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dívidas Ativas</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $stats['dividas_ativas'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Recuperado</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">R$ {{ number_format($stats['valor_recuperado'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Taxa de Sucesso</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ $stats['taxa_sucesso'] }}%</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dívidas por Status</h3>
                <canvas id="statusChart" height="300"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Follow-ups por Tipo</h3>
                <canvas id="tipoChart" height="300"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Status
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['A vencer', 'Vencidas', 'Em negociação', 'Quitadas'],
                    datasets: [{
                        data: [
                            {{ $dividasPorStatus['a_vencer'] ?? 0 }},
                            {{ $dividasPorStatus['vencida'] ?? 0 }},
                            {{ $dividasPorStatus['em_negociacao'] ?? 0 }},
                            {{ $dividasPorStatus['quitada'] ?? 0 }}
                        ],
                        backgroundColor: ['rgb(59, 130, 246)', 'rgb(239, 68, 68)', 'rgb(251, 191, 36)', 'rgb(34, 197, 94)']
                    }]
                }
            });

            // Gráfico de Tipos
            const tipoCtx = document.getElementById('tipoChart').getContext('2d');
            new Chart(tipoCtx, {
                type: 'bar',
                data: {
                    labels: ['Ligação', 'E-mail', 'Retorno', 'Negociação'],
                    datasets: [{
                        label: 'Follow-ups',
                        data: [
                            {{ $followupsPorTipo['ligacao'] ?? 0 }},
                            {{ $followupsPorTipo['email'] ?? 0 }},
                            {{ $followupsPorTipo['retorno'] ?? 0 }},
                            {{ $followupsPorTipo['negociacao'] ?? 0 }}
                        ],
                        backgroundColor: 'rgb(99, 102, 241)'
                    }]
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

