<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $carteira->nome }}</h1>
                <p class="text-gray-600 mt-1">Carteira de cobrança</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('carteiras.edit', $carteira) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    Editar
                </a>
                <form action="{{ route('carteiras.sincronizar', $carteira) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Sincronizar
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informações -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Consultor</dt>
                        <dd class="text-sm text-gray-900">{{ $carteira->consultor->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Credor</dt>
                        <dd class="text-sm text-gray-900">{{ $carteira->credor->razao_social ?? 'Todos' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs rounded-full {{ $carteira->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $carteira->ativo ? 'Ativa' : 'Inativa' }}
                            </span>
                        </dd>
                    </div>
                    @if($carteira->descricao)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Descrição</dt>
                        <dd class="text-sm text-gray-900">{{ $carteira->descricao }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros Aplicados</h2>
                <dl class="space-y-3">
                    @if($carteira->dias_atraso_min || $carteira->dias_atraso_max)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dias de Atraso</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $carteira->dias_atraso_min ?? '0' }} - {{ $carteira->dias_atraso_max ?? '∞' }}
                        </dd>
                    </div>
                    @endif
                    @if($carteira->valor_min || $carteira->valor_max)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Valor</dt>
                        <dd class="text-sm text-gray-900">
                            R$ {{ number_format($carteira->valor_min ?? 0, 2, ',', '.') }} - 
                            R$ {{ $carteira->valor_max ? number_format($carteira->valor_max, 2, ',', '.') : '∞' }}
                        </dd>
                    </div>
                    @endif
                    @if($carteira->status_filtro)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $carteira->status_filtro)) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Dívidas da Carteira -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Dívidas na Carteira ({{ $carteira->dividas->count() }})</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($carteira->dividas as $divida)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $divida->numero_documento }}</p>
                            <p class="text-sm text-gray-600">{{ $divida->cliente->nome }} - {{ $divida->credor->razao_social }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">R$ {{ number_format($divida->valor_atual, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-600">{{ $divida->data_vencimento->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">Nenhuma dívida atribuída a esta carteira</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

