<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $empresa->razao_social }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ $empresa->id }}</p>
            </div>
            <div class="flex gap-3">
                <!-- Botões de ação -->
                <a href="{{ route('empresas.contrato', $empresa) }}"
                   target="_blank"
                   class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                   title="Gerar Contrato">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
                <a href="{{ route('empresas.ficha', $empresa) }}"
                   target="_blank"
                   class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                   title="Gerar Ficha Cadastral">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
                <a href="{{ route('empresas.edit', $empresa) }}"
                   class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                   title="Editar Empresa">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                <a href="{{ route('empresas.index') }}"
                   class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors"
                   title="Voltar à Listagem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Informações da Empresa -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações da Empresa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Razão Social</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->razao_social }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome Fantasia</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->nome_fantasia ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CNPJ</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->cnpj ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-sm">
                        <span class="px-2 py-1 text-xs rounded-full {{ $empresa->status_empresa ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }}">
                            {{ $empresa->status_empresa ? 'Ativo' : 'Inativo' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Contatos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contatos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($empresa->telefone)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Telefone</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->telefone }}</p>
                </div>
                @endif
                @if($empresa->celular)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Celular</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->celular }}</p>
                </div>
                @endif
                @if($empresa->email)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->email }}</p>
                </div>
                @endif
                @if($empresa->email_financeiro)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email Financeiro</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $empresa->email_financeiro }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Total de Devedores</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $empresa->devedores->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Total de Títulos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $empresa->titulos->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Títulos Pendentes</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">
                    {{ $empresa->titulos->where('statusBaixa', 0)->count() }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
