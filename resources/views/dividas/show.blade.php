<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dívida #{{ $divida->numero_documento }}</h1>
                <p class="text-gray-600 mt-1">{{ $divida->cliente->nome }} - {{ $divida->credor->razao_social }}</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->canViewAllDividas())
                <a href="{{ route('dividas.edit', $divida) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    Editar
                </a>
                @endif
                <a href="{{ route('followups.create', ['divida_id' => $divida->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Novo Follow-up
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informações da Dívida -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Valor Original</dt>
                        <dd class="text-lg font-bold text-gray-900">R$ {{ number_format($divida->valor_original, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Valor Atual</dt>
                        <dd class="text-lg font-bold text-gray-900">R$ {{ number_format($divida->valor_atual, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data de Vencimento</dt>
                        <dd class="text-sm text-gray-900">{{ $divida->data_vencimento->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dias em Atraso</dt>
                        <dd class="text-sm text-gray-900">{{ $divida->dias_atraso }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd>
                            @php
                                $statusColors = [
                                    'a_vencer' => 'bg-blue-100 text-blue-800',
                                    'vencida' => 'bg-red-100 text-red-800',
                                    'em_negociacao' => 'bg-yellow-100 text-yellow-800',
                                    'quitada' => 'bg-green-100 text-green-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$divida->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $divida->status)) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Informações do Cliente -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome</dt>
                        <dd class="text-sm text-gray-900">{{ $divida->cliente->nome }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">CPF/CNPJ</dt>
                        <dd class="text-sm text-gray-900">{{ $divida->cliente->documento ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                        <dd class="text-sm text-gray-900">{{ $divida->cliente->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                        <dd class="text-sm text-gray-900">{{ $divida->cliente->telefone ?? $divida->cliente->celular ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Follow-ups -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Follow-ups</h2>
                <a href="{{ route('followups.create', ['divida_id' => $divida->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    + Novo Follow-up
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($divida->followups as $followup)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ ucfirst($followup->tipo) }}</p>
                            <p class="text-sm text-gray-600">Previsto para {{ $followup->data_prevista->format('d/m/Y') }}</p>
                            @if($followup->resultado)
                            <p class="text-sm text-gray-500 mt-1">{{ $followup->resultado }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $followup->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($followup->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">Nenhum follow-up cadastrado</div>
                @endforelse
            </div>
        </div>

        <!-- Histórico -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Histórico de Cobrança</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($divida->historicoCobranca as $historico)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $historico->tipo_acao)) }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $historico->descricao }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $historico->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">Nenhum histórico registrado</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

