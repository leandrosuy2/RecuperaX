<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Criar Agendamento</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Agendar retorno com devedor</p>
            </div>
            <a href="{{ route('agendamentos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        @if($devedor)
        <!-- Informações do Devedor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Devedor Selecionado</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->nome_completo }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CPF/CNPJ</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $devedor->documento }}</p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('agendamentos.store') }}" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Devedor *</label>
                    <select name="devedor_id" id="devedor_id" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach($devedores as $d)
                        <option value="{{ $d->id }}" {{ ($devedor && $devedor->id == $d->id) ? 'selected' : '' }}>
                            {{ $d->nome_completo }}
                        </option>
                        @endforeach
                    </select>
                    @error('devedor_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa *</label>
                    <select name="empresa_id" id="empresa_id" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ ($devedor && $devedor->empresa_id == $empresa->id) ? 'selected' : '' }}>
                            {{ $empresa->nome_fantasia ?? $empresa->razao_social }}
                        </option>
                        @endforeach
                    </select>
                    @error('empresa_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data e Hora de Retorno *</label>
                    <input type="datetime-local" name="data_retorno" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('data_retorno')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                    <input type="text" name="telefone" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operador</label>
                    <input type="text" name="operador" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assunto *</label>
                    <textarea name="assunto" rows="3" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    @error('assunto')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('agendamentos.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Criar Agendamento
                </button>
            </div>
        </form>
    </div>

    <script>
        // Auto-preenche empresa quando selecionar devedor
        document.getElementById('devedor_id').addEventListener('change', function() {
            const devedorId = this.value;
            if (devedorId) {
                // Buscar empresa do devedor via AJAX
                fetch(`/devedores/${devedorId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.empresa_id) {
                            document.getElementById('empresa_id').value = data.empresa_id;
                        }
                    })
                    .catch(() => {
                        // Ignorar erro
                    });
            }
        });
    </script>
</x-app-layout>
