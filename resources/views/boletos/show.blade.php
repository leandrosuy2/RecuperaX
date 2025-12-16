<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Boleto #{{ $boleto->id }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Detalhes do boleto</p>
            </div>
            <a href="{{ route('boletos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <!-- Informações do Boleto -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Boleto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Empresa</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $boleto->empresa ? ($boleto->empresa->nome_fantasia ?? $boleto->empresa->razao_social) : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valor</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($boleto->valor, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Emissão</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $boleto->data_emissao ? $boleto->data_emissao->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data de Vencimento</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $boleto->data_vencimento ? $boleto->data_vencimento->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Situação</p>
                    <p class="text-sm">
                        <span class="px-2 py-1 text-xs rounded-full {{ $boleto->situacao === 'PAGO' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($boleto->situacao === 'VENCIDO' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200') }}">
                            {{ $boleto->situacao }}
                        </span>
                    </p>
                </div>
                @if($boleto->nosso_numero)
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nosso Número</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $boleto->nosso_numero }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Ações -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações</h2>
            <div class="flex gap-3">
                <a href="{{ route('boletos.baixar-pdf', $boleto) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Baixar PDF
                </a>
                @if($boleto->pix_copia_e_cola)
                <a href="{{ route('boletos.qr-code', $boleto) }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                    Ver QR Code PIX
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
