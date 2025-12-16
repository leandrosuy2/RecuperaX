<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $cliente->nome }}</h1>
                <p class="text-gray-600 mt-1">{{ $cliente->documento ?? 'Sem documento' }}</p>
            </div>
            @if(auth()->user()->canViewAllDividas())
            <a href="{{ route('clientes.edit', $cliente) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                Editar
            </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações de Contato</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                        <dd class="text-sm text-gray-900">{{ $cliente->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                        <dd class="text-sm text-gray-900">{{ $cliente->telefone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Celular</dt>
                        <dd class="text-sm text-gray-900">{{ $cliente->celular ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total de Dívidas</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $cliente->dividas->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Valor Total</dt>
                        <dd class="text-2xl font-bold text-red-600">R$ {{ number_format($cliente->total_dividas, 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Dívidas -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Dívidas</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($cliente->dividas as $divida)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $divida->numero_documento }}</p>
                            <p class="text-sm text-gray-600">{{ $divida->credor->razao_social }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">R$ {{ number_format($divida->valor_atual, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-600">{{ $divida->data_vencimento->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">Nenhuma dívida cadastrada</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

