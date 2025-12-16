<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Agendamento #{{ $agendamento->id }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Devedor: {{ $agendamento->devedor->nome_completo }}</p>
            </div>
            <div class="flex gap-2">
                @if($agendamento->status === 'Pendente')
                <form action="{{ route('agendamentos.finalizar', $agendamento) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                        Finalizar
                    </button>
                </form>
                @endif
                <a href="{{ route('agendamentos.edit', $agendamento) }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Editar
                </a>
                <a href="{{ route('agendamentos.index') }}" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    ← Voltar
                </a>
            </div>
        </div>

        <!-- Informações do Agendamento -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Agendamento</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Devedor</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->devedor->nome_completo }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Empresa</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $agendamento->empresa ? ($agendamento->empresa->nome_fantasia ?? $agendamento->empresa->razao_social) : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Abertura</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->data_abertura->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Retorno</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->data_retorno->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-sm">
                        @if($agendamento->status === 'Finalizado')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Finalizado</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Pendente</span>
                        @endif
                    </p>
                </div>
                @if($agendamento->telefone)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Telefone</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->telefone }}</p>
                </div>
                @endif
                @if($agendamento->operador)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Operador</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->operador }}</p>
                </div>
                @endif
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Assunto</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $agendamento->assunto }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
