<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Pagamento {{ $pagamento->numero_transacao }}</h1>
                <p class="text-gray-600 mt-1">{{ $pagamento->cliente->nome }}</p>
            </div>
            @if($pagamento->status === 'pendente' && (auth()->user()->canViewAllDividas() || auth()->user()->id === $pagamento->consultor_id))
            <form action="{{ route('pagamentos.confirmar', $pagamento) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Confirmar Pagamento
                </button>
            </form>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Pagamento</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Valor</dt>
                        <dd class="text-2xl font-bold text-gray-900">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data do Pagamento</dt>
                        <dd class="text-sm text-gray-900">{{ $pagamento->data_pagamento->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Forma de Pagamento</dt>
                        <dd class="text-sm text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $pagamento->forma_pagamento)) }}
                            @if($pagamento->isPicPay())
                                <a href="{{ route('pagamentos.picpay', $pagamento) }}" class="ml-2 text-indigo-600 hover:text-indigo-800 text-xs underline">
                                    Ver QR Code
                                </a>
                            @endif
                        </dd>
                    </div>
                    @if($pagamento->isPicPay())
                    <div>
                        <dt class="text-sm font-medium text-gray-500">PicPay Reference ID</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $pagamento->picpay_reference_id }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd>
                            @php
                                $statusColors = [
                                    'pendente' => 'bg-yellow-100 text-yellow-800',
                                    'confirmado' => 'bg-green-100 text-green-800',
                                    'cancelado' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$pagamento->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($pagamento->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações Relacionadas</h2>
                <dl class="space-y-3">
                    @if($pagamento->acordo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Acordo</dt>
                        <dd class="text-sm text-gray-900">{{ $pagamento->acordo->numero_acordo }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dívida</dt>
                        <dd class="text-sm text-gray-900">{{ $pagamento->divida->numero_documento }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                        <dd class="text-sm text-gray-900">{{ $pagamento->cliente->nome }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>

