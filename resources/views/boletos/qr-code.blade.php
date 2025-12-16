<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">QR Code PIX</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Boleto #{{ $boleto->id }}</p>
            </div>
            <a href="{{ route('boletos.show', $boleto) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="max-w-md mx-auto text-center space-y-4">
                <div class="bg-gray-100 dark:bg-gray-700 p-8 rounded-lg">
                    <!-- QR Code será gerado aqui via JavaScript ou API -->
                    <div id="qrcode" class="mx-auto"></div>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código PIX (Copia e Cola)</p>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-900 dark:text-gray-100 break-all font-mono">{{ $boleto->pix_copia_e_cola }}</p>
                    </div>
                    <button onclick="copiarPix()" class="mt-2 w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                        Copiar Código PIX
                    </button>
                </div>

                <div class="text-left space-y-2">
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Valor</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($boleto->valor, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Vencimento</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $boleto->data_vencimento ? $boleto->data_vencimento->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // TODO: Integrar biblioteca de geração de QR Code (ex: qrcode.js)
        function copiarPix() {
            const texto = '{{ $boleto->pix_copia_e_cola }}';
            navigator.clipboard.writeText(texto).then(() => {
                alert('Código PIX copiado!');
            });
        }
    </script>
</x-app-layout>
