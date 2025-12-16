<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Relatório - {{ $credor->razao_social }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Estatísticas e recuperação por credor</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 transition-colors">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="credor_id" value="{{ $credor->id }}">
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
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor em Atraso</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">R$ {{ number_format($stats['valor_total_em_atraso'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor Recuperado</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">R$ {{ number_format($stats['valor_total_recuperado'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Acordos Ativos</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ $stats['acordos_ativos'] }}</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dívidas por Status</h3>
                <canvas id="statusChart" height="300"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recuperação Mensal</h3>
                <canvas id="recuperacaoChart" height="300"></canvas>
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

            // Gráfico de Recuperação
            const recuperacaoCtx = document.getElementById('recuperacaoChart').getContext('2d');
            const meses = {!! json_encode(array_keys($recuperacaoMensal->toArray())) !!};
            const valores = {!! json_encode(array_values($recuperacaoMensal->toArray())) !!};
            
            new Chart(recuperacaoCtx, {
                type: 'line',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Valor Recuperado (R$)',
                        data: valores,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + (value / 1000) + 'k';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

