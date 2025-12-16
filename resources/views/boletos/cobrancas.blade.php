<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Cobranças Geradas</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                    Histórico de cobranças e boletos emitidos
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('emitir-boletos') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Emitir Boletos
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Empresa, CNPJ, ID ou valor..."
                               class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Buscar
                    </button>
                    <a href="{{ route('listar-cobrancas') }}" class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabela de Cobranças -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anexo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pago</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Criado em</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($cobrancas as $cobranca)
                        <tr class="{{ $cobranca->pago ? 'bg-green-50 dark:bg-green-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $cobranca->id }}</td>
                            <td class="px-4 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cobranca->empresa->razao_social }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ substr($cobranca->empresa->cnpj, 0, 2) }}.{{ substr($cobranca->empresa->cnpj, 2, 3) }}.{{ substr($cobranca->empresa->cnpj, 5, 3) }}/{{ substr($cobranca->empresa->cnpj, 8, 4) }}-{{ substr($cobranca->empresa->cnpj, 12, 2) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $cobranca->data_cobranca->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 font-semibold">
                                R$ {{ number_format($cobranca->valor_comissao, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($cobranca->tipo_anexo === 'documento') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                    {{ ucfirst($cobranca->tipo_anexo) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($cobranca->tipo_anexo === 'documento' && $cobranca->documento)
                                    <button onclick="baixarDocumento({{ $cobranca->id }})"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        Baixar
                                    </button>
                                @elseif($cobranca->tipo_anexo === 'link' && $cobranca->link)
                                    <a href="{{ $cobranca->link }}" target="_blank"
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        Abrir
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           {{ $cobranca->pago ? 'checked' : '' }}
                                           onchange="atualizarStatusPago({{ $cobranca->id }}, this.checked)"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $cobranca->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma cobrança encontrada
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($cobrancas->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $cobrancas->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        function baixarDocumento(cobrancaId) {
            window.open(`/cobrancas/${cobrancaId}/baixar-documento`, '_blank');
        }

        function atualizarStatusPago(cobrancaId, pago) {
            fetch(`/cobrancas/${cobrancaId}/atualizar-pago`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ pago: pago })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar visual da linha
                    const row = event.target.closest('tr');
                    if (pago) {
                        row.classList.add('bg-green-50', 'dark:bg-green-900/10');
                    } else {
                        row.classList.remove('bg-green-50', 'dark:bg-green-900/10');
                    }
                } else {
                    alert('Erro ao atualizar status: ' + data.message);
                    // Reverter checkbox
                    event.target.checked = !pago;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao atualizar status');
                // Reverter checkbox
                event.target.checked = !pago;
            });
        }
    </script>
</x-app-layout>