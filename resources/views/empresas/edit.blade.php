<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Editar Empresa</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">ID: {{ $empresa->id }}</p>
            </div>
            <a href="{{ route('empresas.show', $empresa) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <form method="POST" action="{{ route('empresas.update', $empresa) }}" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Dados Básicos -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dados Básicos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Razão Social *</label>
                        <input type="text" name="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Minha Empresa LTDA">
                        @error('razao_social')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Fantasia *</label>
                        <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Minha Empresa">
                        @error('nome_fantasia')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CNPJ *</label>
                        <input type="text" name="cnpj" value="{{ old('cnpj', $empresa->cnpj) }}" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="00.000.000/0000-00">
                        @error('cnpj')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inscrição Estadual</label>
                        <input type="text" name="ie" value="{{ old('ie', $empresa->ie) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 00123456789">
                    </div>
                </div>
            </div>

            <!-- Contato -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contato</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Contato</label>
                        <input type="text" name="nome_contato" value="{{ old('nome_contato', $empresa->nome_contato) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: João Silva">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF do Contato</label>
                        <input type="text" name="cpf_contato" value="{{ old('cpf_contato', $empresa->cpf_contato) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="000.000.000-00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $empresa->telefone) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(00) 0000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Celular</label>
                        <input type="text" name="celular" value="{{ old('celular', $empresa->celular) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(00) 00000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp Financeiro</label>
                        <input type="text" name="whatsapp_financeiro" value="{{ old('whatsapp_financeiro', $empresa->whatsapp_financeiro) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(00) 00000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $empresa->email) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="contato@empresa.com">
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Financeiro</label>
                        <input type="email" name="email_financeiro" value="{{ old('email_financeiro', $empresa->email_financeiro) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="financeiro@empresa.com">
                        @error('email_financeiro')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Responsáveis -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Responsáveis</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operador</label>
                        <input type="text" name="operador" value="{{ old('operador', $empresa->operador) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do operador responsável">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor</label>
                        <input type="text" name="supervisor" value="{{ old('supervisor', $empresa->supervisor) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do supervisor responsável">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gerente</label>
                        <input type="text" name="gerente" value="{{ old('gerente', $empresa->gerente) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do gerente responsável">
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Endereço</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                        <input type="text" name="cep" value="{{ old('cep', $empresa->cep) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="00000-000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
                        <input type="text" name="endereco" value="{{ old('endereco', $empresa->endereco) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Rua, Avenida, Alameda...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número</label>
                        <input type="text" name="numero" value="{{ old('numero', $empresa->numero) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="123">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bairro</label>
                        <input type="text" name="bairro" value="{{ old('bairro', $empresa->bairro) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Centro, Jardim...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                        <input type="text" name="cidade" value="{{ old('cidade', $empresa->cidade) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="São Paulo, Rio de Janeiro...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UF</label>
                        <input type="text" name="uf" maxlength="2" value="{{ old('uf', $empresa->uf) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="SP, RJ, MG...">
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Configurações</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plano (Tabela de Remuneração)</label>
                        <select name="plano_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione...</option>
                            @foreach($planos as $plano)
                            <option value="{{ $plano->id }}" {{ $empresa->plano_id == $plano->id ? 'selected' : '' }}>{{ $plano->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status da Empresa</label>
                        <select name="status_empresa" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" {{ $empresa->status_empresa ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ !$empresa->status_empresa ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                        @if($empresa->logo)
                        <div class="mb-2">
                            <img src="{{ Storage::url($empresa->logo) }}" alt="Logo" class="h-16 w-16 object-contain">
                        </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('logo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Dados Bancários -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dados Bancários</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banco</label>
                        <input type="text" name="banco" value="{{ old('banco', $empresa->banco) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Banco do Brasil">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agência</label>
                        <input type="text" name="agencia" value="{{ old('agencia', $empresa->agencia) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 1234">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conta</label>
                        <input type="text" name="conta" value="{{ old('conta', $empresa->conta) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 12345-6">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Favorecido PIX</label>
                        <input type="text" name="nome_favorecido_pix" value="{{ old('nome_favorecido_pix', $empresa->nome_favorecido_pix) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome completo do favorecido">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Chave PIX</label>
                        <select name="tipo_pix" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione...</option>
                            <option value="CNPJ" {{ old('tipo_pix', $empresa->tipo_pix) == 'CNPJ' ? 'selected' : '' }}>CNPJ</option>
                            <option value="CPF" {{ old('tipo_pix', $empresa->tipo_pix) == 'CPF' ? 'selected' : '' }}>CPF</option>
                            <option value="EMAIL" {{ old('tipo_pix', $empresa->tipo_pix) == 'EMAIL' ? 'selected' : '' }}>Email</option>
                            <option value="TELEFONE" {{ old('tipo_pix', $empresa->tipo_pix) == 'TELEFONE' ? 'selected' : '' }}>Telefone</option>
                            <option value="CHAVE_ALEATORIA" {{ old('tipo_pix', $empresa->tipo_pix) == 'CHAVE_ALEATORIA' ? 'selected' : '' }}>Chave Aleatória</option>
                            <option value="AGENCIA_CONTA" {{ old('tipo_pix', $empresa->tipo_pix) == 'AGENCIA_CONTA' ? 'selected' : '' }}>Agência e Conta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chave PIX</label>
                        <input type="text" name="chave_pix" value="{{ old('chave_pix', $empresa->chave_pix) }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Chave PIX conforme o tipo selecionado">
                    </div>
                </div>
            </div>

            <!-- Condições de Negociação -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Condições de Negociação aos Clientes Devedores</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade de Parcelas</label>
                        <input type="number" name="qtd_parcelas" value="{{ old('qtd_parcelas', $empresa->qtd_parcelas) }}" min="1" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 12">
                        @error('qtd_parcelas')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desconto Total à Vista (%)</label>
                        <input type="number" name="desconto_total_avista" value="{{ old('desconto_total_avista', $empresa->desconto_total_avista) }}" min="0" max="100" step="0.01" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 10.00">
                        @error('desconto_total_avista')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desconto Total a Prazo (%)</label>
                        <input type="number" name="desconto_total_aprazo" value="{{ old('desconto_total_aprazo', $empresa->desconto_total_aprazo) }}" min="0" max="100" step="0.01" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 5.00">
                        @error('desconto_total_aprazo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex gap-3 justify-end">
                <a href="{{ route('empresas.show', $empresa) }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Atualizar Empresa
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
