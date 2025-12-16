<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Adicionar Empresa</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Cadastrar nova empresa credora</p>
            </div>
            <a href="{{ route('empresas.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                ← Voltar
            </a>
        </div>

        <form method="POST" action="{{ route('empresas.store') }}" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <!-- Dados Básicos -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dados Básicos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Razão Social *</label>
                        <input type="text" name="razao_social" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="razao_social" placeholder="Ex: Minha Empresa LTDA">
                        @error('razao_social')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Fantasia *</label>
                        <input type="text" name="nome_fantasia" required class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="nome_fantasia" placeholder="Ex: Minha Empresa">
                        @error('nome_fantasia')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CNPJ *</label>
                        <div class="flex gap-2">
                            <input type="text" name="cnpj" required class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="cnpj" placeholder="00.000.000/0000-00">
                            <button type="button" onclick="consultarCnpj()" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition-colors" id="btn-consultar-cnpj">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('cnpj')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inscrição Estadual</label>
                        <input type="text" name="ie" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="ie" placeholder="Ex: 00123456789">
                    </div>
                </div>
            </div>

            <!-- Contato -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contato</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Contato</label>
                        <input type="text" name="nome_contato" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: João Silva">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF do Contato</label>
                        <input type="text" name="cpf_contato" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="000.000.000-00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                        <input type="text" name="telefone" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(00) 0000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Celular</label>
                        <input type="text" name="celular" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(00) 00000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp Financeiro</label>
                        <input type="text" name="whatsapp_financeiro" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(00) 00000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="contato@empresa.com">
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Financeiro</label>
                        <input type="email" name="email_financeiro" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="financeiro@empresa.com">
                        @error('email_financeiro')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Dados Bancários -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dados Bancários</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banco</label>
                        <input type="text" name="banco" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Banco do Brasil">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agência</label>
                        <input type="text" name="agencia" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 1234">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conta</label>
                        <input type="text" name="conta" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 12345-6">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome do Favorecido PIX</label>
                        <input type="text" name="nome_favorecido_pix" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome completo do favorecido">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Chave PIX</label>
                        <select name="tipo_pix" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione...</option>
                            <option value="CNPJ">CNPJ</option>
                            <option value="CPF">CPF</option>
                            <option value="EMAIL">Email</option>
                            <option value="TELEFONE">Telefone</option>
                            <option value="CHAVE_ALEATORIA">Chave Aleatória</option>
                            <option value="AGENCIA_CONTA">Agência e Conta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chave PIX</label>
                        <input type="text" name="chave_pix" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Chave PIX conforme o tipo selecionado">
                    </div>
                </div>
            </div>

            <!-- Responsáveis -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Responsáveis</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operador</label>
                        <input type="text" name="operador" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do operador responsável">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor</label>
                        <input type="text" name="supervisor" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do supervisor responsável">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gerente</label>
                        <input type="text" name="gerente" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do gerente responsável">
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Endereço</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                        <input type="text" name="cep" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="00000-000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
                        <input type="text" name="endereco" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Rua, Avenida, Alameda...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número</label>
                        <input type="text" name="numero" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="123">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bairro</label>
                        <input type="text" name="bairro" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Centro, Jardim...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                        <input type="text" name="cidade" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="São Paulo, Rio de Janeiro...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UF</label>
                        <input type="text" name="uf" maxlength="2" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="SP, RJ, MG...">
                    </div>
                </div>
            </div>

            <!-- Condições de Negociação -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Condições de Negociação aos Clientes Devedores</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade de Parcelas</label>
                        <input type="number" name="qtd_parcelas" min="1" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 12">
                        @error('qtd_parcelas')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desconto Total à Vista (%)</label>
                        <input type="number" name="desconto_total_avista" min="0" max="100" step="0.01" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 10.00">
                        @error('desconto_total_avista')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desconto Total a Prazo (%)</label>
                        <input type="number" name="desconto_total_aprazo" min="0" max="100" step="0.01" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: 5.00">
                        @error('desconto_total_aprazo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Configurações</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor de Adesão/Implantação</label>
                        <input type="text" name="valor_adesao" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="R$ 0,00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plano (Tabela de Remuneração)</label>
                        <select name="plano_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione...</option>
                            @foreach($planos as $plano)
                            <option value="{{ $plano->id }}">{{ $plano->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status da Empresa</label>
                        <select name="status_empresa" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1" selected>Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('logo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex gap-3 justify-end">
                <a href="{{ route('empresas.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Salvar Empresa
                </button>
            </div>
        </form>

        <!-- JavaScript para máscaras e funcionalidades -->
        <script>
            // Máscaras para os campos
            document.addEventListener('DOMContentLoaded', function() {
                // Máscara CNPJ
                const cnpjInput = document.getElementById('cnpj');
                if (cnpjInput) {
                    cnpjInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length <= 14) {
                            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                            e.target.value = value;
                        }
                    });
                }

                // Máscara CPF
                const cpfInput = document.getElementById('cpf_contato');
                if (cpfInput) {
                    cpfInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length <= 11) {
                            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                            e.target.value = value;
                        }
                    });
                }

                // Máscara Telefone/Celular
                const phoneInputs = ['telefone', 'celular', 'whatsapp_financeiro'];
                phoneInputs.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.addEventListener('input', function(e) {
                            let value = e.target.value.replace(/\D/g, '');
                            if (value.length <= 11) {
                                if (value.length <= 10) {
                                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                                } else {
                                    value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                                }
                                e.target.value = value;
                            }
                        });
                    }
                });

                // Máscara CEP
                const cepInput = document.getElementById('cep');
                if (cepInput) {
                    cepInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length <= 8) {
                            value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
                            e.target.value = value;
                        }
                    });
                }
            });

            // Função para consultar CNPJ
            function consultarCnpj() {
                const cnpjInput = document.getElementById('cnpj');
                const btnConsultar = document.getElementById('btn-consultar-cnpj');

                if (!cnpjInput || !cnpjInput.value) {
                    if (window.toast) {
                        window.toast.error('Digite um CNPJ válido');
                    }
                    return;
                }

                const cnpj = cnpjInput.value.replace(/\D/g, '');
                if (cnpj.length !== 14) {
                    if (window.toast) {
                        window.toast.error('CNPJ deve ter 14 dígitos');
                    }
                    return;
                }

                // Desabilitar botão durante consulta
                btnConsultar.disabled = true;
                btnConsultar.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                fetch('/empresas/consultar-cnpj', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cnpj: cnpj })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        // Preencher campos com dados da API
                        if (data.data.razao_social) document.getElementById('razao_social').value = data.data.razao_social;
                        if (data.data.nome_fantasia) document.getElementById('nome_fantasia').value = data.data.nome_fantasia;
                        if (data.data.inscricao_estadual) document.getElementById('ie').value = data.data.inscricao_estadual;

                        // Endereço
                        if (data.data.cep) document.getElementById('cep').value = data.data.cep;
                        if (data.data.logradouro) document.getElementById('endereco').value = data.data.logradouro;
                        if (data.data.numero) document.getElementById('numero').value = data.data.numero;
                        if (data.data.bairro) document.getElementById('bairro').value = data.data.bairro;
                        if (data.data.municipio) document.getElementById('cidade').value = data.data.municipio;
                        if (data.data.uf) document.getElementById('uf').value = data.data.uf;

                        // Contato
                        if (data.data.ddd_telefone_1 && data.data.telefone_1) {
                            document.getElementById('telefone').value = `(${data.data.ddd_telefone_1}) ${data.data.telefone_1}`;
                        }

                        if (window.toast) {
                            window.toast.success('Dados do CNPJ preenchidos automaticamente');
                        }
                    } else {
                        if (window.toast) {
                            window.toast.error(data.message || 'Erro ao consultar CNPJ');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    if (window.toast) {
                        window.toast.error('Erro ao consultar CNPJ');
                    }
                })
                .finally(() => {
                    // Reabilitar botão
                    btnConsultar.disabled = false;
                    btnConsultar.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
                });
            }
        </script>
    </div>
</x-app-layout>
