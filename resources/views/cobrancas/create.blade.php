<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Nova Cobrança</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Registrar cobrança de comissão</p>
            </div>
            <a href="{{ route('cobrancas.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <form method="POST" action="{{ route('cobrancas.store') }}" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa *</label>
                    <select name="empresa_id" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nome_fantasia ?? $empresa->razao_social }}</option>
                        @endforeach
                    </select>
                    @error('empresa_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Cobrança *</label>
                    <input type="date" name="data_cobranca" value="{{ date('Y-m-d') }}" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('data_cobranca')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor da Comissão *</label>
                    <input type="number" name="valor_comissao" step="0.01" min="0" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('valor_comissao')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Anexo *</label>
                    <select name="tipo_anexo" id="tipo_anexo" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                        <option value="documento">Documento (PDF/Imagem)</option>
                        <option value="link">Link</option>
                    </select>
                    @error('tipo_anexo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div id="campo_documento" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Documento</label>
                    <input type="file" name="documento" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('documento')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div id="campo_link" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link</label>
                    <input type="url" name="link" placeholder="https://..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('link')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('cobrancas.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Criar Cobrança
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('tipo_anexo').addEventListener('change', function() {
            const tipo = this.value;
            const campoDocumento = document.getElementById('campo_documento');
            const campoLink = document.getElementById('campo_link');
            
            campoDocumento.style.display = tipo === 'documento' ? 'block' : 'none';
            campoLink.style.display = tipo === 'link' ? 'block' : 'none';
        });
    </script>
</x-app-layout>
