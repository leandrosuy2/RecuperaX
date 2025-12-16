<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Conectar WhatsApp — Negociados</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Enviar mensagens para devedores com acordos ativos</p>
            </div>
        </div>

        <!-- Lista de Devedores -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Devedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acordos Ativos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($devedores as $devedor)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $devedor->nome }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $devedor->cpf_cnpj }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $devedor->telefone ?? 'Não informado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $devedor->acordos->count() }} acordo(s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    R$ {{ number_format($devedor->acordos->sum('valor_acordo'), 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($devedor->telefone)
                                        <button onclick="enviarMensagem({{ $devedor->id }}, '{{ $devedor->nome }}')"
                                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-3 py-1 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            Enviar Mensagem
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-sm">Sem telefone</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum devedor com acordos ativos encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($devedores->hasPages())
                <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    {{ $devedores->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para enviar mensagem -->
    <div id="mensagemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Enviar Mensagem WhatsApp</h3>
                <form id="mensagemForm" action="{{ route('whatsapp.enviar-mensagem') }}" method="POST">
                    @csrf
                    <input type="hidden" name="devedor_id" id="devedor_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Destinatário</label>
                        <p id="destinatarioNome" class="text-sm text-gray-900 dark:text-gray-100"></p>
                    </div>
                    <div class="mb-4">
                        <label for="mensagem" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mensagem</label>
                        <textarea name="mensagem" id="mensagem" rows="4"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                  placeholder="Digite sua mensagem..." required></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                            Enviar
                        </button>
                        <button type="button" onclick="fecharModal()"
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium px-4 py-2 rounded-lg">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function enviarMensagem(devedorId, nome) {
            document.getElementById('devedor_id').value = devedorId;
            document.getElementById('destinatarioNome').textContent = nome;
            document.getElementById('mensagemModal').classList.remove('hidden');
        }

        function fecharModal() {
            document.getElementById('mensagemModal').classList.add('hidden');
            document.getElementById('mensagemForm').reset();
        }
    </script>
</x-app-layout>