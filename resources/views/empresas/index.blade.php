<x-app-layout>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Empresas</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Gerenciar empresas credoras</p>
            </div>
            <a href="{{ route('empresas.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Empresa
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 transition-colors">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pesquisar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Razão social, CNPJ..." 
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status_empresa" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        <option value="">Todos</option>
                        <option value="1" {{ request('status_empresa') == '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ request('status_empresa') == '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Filtrar</button>
                    @if(request()->anyFilled(['search', 'status_empresa']))
                    <a href="{{ route('empresas.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm px-4 py-2 rounded-lg transition-colors">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Razão Social</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden md:table-cell">Nome Fantasia</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase hidden lg:table-cell">CNPJ</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($empresas as $empresa)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $empresa->id }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $empresa->razao_social }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden md:table-cell">{{ $empresa->nome_fantasia ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 hidden lg:table-cell">{{ $empresa->cnpj ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <button type="button"
                                        onclick="toggleStatus({{ $empresa->id }}, {{ $empresa->status_empresa ? 'true' : 'false' }})"
                                        class="px-2 py-1 text-xs rounded-full transition-colors {{ $empresa->status_empresa ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800' }}"
                                        title="Clique para {{ $empresa->status_empresa ? 'desativar' : 'ativar' }} empresa">
                                    {{ $empresa->status_empresa ? 'Ativo' : 'Inativo' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('empresas.contrato', $empresa) }}"
                                       target="_blank"
                                       class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                       title="Gerar Contrato">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('empresas.show', $empresa) }}"
                                       class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                                       title="Ver detalhes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('empresas.edit', $empresa) }}"
                                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma empresa encontrada
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($empresas->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $empresas->links() }}
            </div>
            @endif
        </div>

        <!-- JavaScript para toggle de status -->
        <script>
            function toggleStatus(empresaId, currentStatus) {
                const button = event.target;
                const originalText = button.textContent;
                const originalClasses = button.className;

                // Desabilitar botão durante a requisição
                button.disabled = true;
                button.textContent = 'Alterando...';

                fetch(`/empresas/${empresaId}/alterar-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar aparência do botão
                        const newStatus = data.status_empresa;
                        button.textContent = newStatus ? 'Ativo' : 'Inativo';
                        button.className = `px-2 py-1 text-xs rounded-full transition-colors ${newStatus ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800'}`;
                        button.setAttribute('onclick', `toggleStatus(${empresaId}, ${newStatus})`);
                        button.setAttribute('title', `Clique para ${newStatus ? 'desativar' : 'ativar'} empresa`);

                        // Mostrar toast de sucesso
                        if (window.toast) {
                            window.toast.success(data.message);
                        }
                    } else {
                        throw new Error('Erro na resposta');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    // Restaurar estado original
                    button.disabled = false;
                    button.textContent = originalText;
                    button.className = originalClasses;

                    if (window.toast) {
                        window.toast.error('Erro ao alterar status da empresa');
                    }
                });
            }
        </script>
    </div>
</x-app-layout>
