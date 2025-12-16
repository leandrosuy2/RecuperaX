<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Cadastral - {{ $empresa->nome_fantasia }}</title>
    <style>
        @page {
            size: A4;
            margin: 2.5cm 2cm 2cm 3cm;
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 9pt;
                color: #666;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #2c3e50;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .document-header {
            position: relative;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3498db;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo {
            max-width: 100px;
            max-height: 60px;
            object-fit: contain;
        }

        .company-info {
            text-align: center;
        }

        .main-title {
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .subtitle {
            font-size: 12pt;
            color: #7f8c8d;
            font-weight: normal;
        }

        .document-type {
            font-size: 10pt;
            color: #95a5a6;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-header {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 6px 12px;
            margin-bottom: 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .section-header::before {
            content: "";
            margin-right: 8px;
            font-size: 12pt;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .field-group {
            display: flex;
            margin-bottom: 6px;
            align-items: center;
            min-height: 24px;
        }

        .field-label {
            flex: 0 0 180px;
            font-weight: bold;
            color: #34495e;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding-right: 8px;
        }

        .field-value {
            flex: 1;
            border-bottom: 1px dotted #bdc3c7;
            padding: 4px 8px;
            background: #f8f9fa;
            border-radius: 2px;
            font-size: 10pt;
            min-height: 20px;
            display: flex;
            align-items: center;
        }

        .field-value.empty {
            color: #95a5a6;
            font-style: italic;
        }

        .highlight-value {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12pt;
            color: #2c3e50;
            text-align: center;
            margin: 10px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .contact-info {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin: 10px 0;
            border-radius: 0 4px 4px 0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }

        .contact-icon {
            width: 16px;
            margin-right: 8px;
            opacity: 0.7;
        }

        .banking-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
        }

        .banking-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            padding: 20px;
            border-top: 2px solid #2c3e50;
            position: relative;
            margin-top: 30px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .signature-box::before {
            content: "_______________________________";
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            color: #6c757d;
            font-size: 10pt;
            letter-spacing: 1px;
        }

        .signature-title {
            font-weight: bold;
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .signature-detail {
            font-size: 9pt;
            color: #6c757d;
            line-height: 1.4;
        }

        .document-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 9pt;
            color: #6c757d;
        }

        .cnpj-format {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10pt;
            letter-spacing: 0.5px;
        }

        .observacoes-section {
            margin-top: 20px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            min-height: 80px;
            background: #f8f9fa;
        }

        .observacoes-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .observacoes-content {
            font-style: italic;
            color: #6c757d;
            line-height: 1.5;
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        @media print {
            body {
                font-size: 10pt;
            }

            .field-value {
                background: white !important;
                -webkit-print-color-adjust: exact;
            }

            .highlight-value {
                -webkit-print-color-adjust: exact;
            }

            .print-controls {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Controles de Impressão -->
    <div class="print-controls">
        <button onclick="window.print()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors" title="Imprimir Ficha">
            Imprimir
        </button>
        <button onclick="downloadPDF()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors" title="Baixar PDF">
            Salvar PDF
        </button>
        <button onclick="window.close()" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300" title="Fechar">
            Fechar
        </button>
    </div>

    <!-- Cabeçalho do Documento -->
    <div class="document-header">
        <div class="logo-container">
            @if($empresa->logo)
                <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo {{ $empresa->nome_fantasia }}" class="logo">
            @endif
            <div class="company-info">
                <div class="main-title">RecuperaX</div>
                <div class="subtitle">Sistema de Cobrança e Recuperação</div>
            </div>
        </div>
        <div class="main-title">Ficha Cadastral</div>
        <div class="document-type">
            Cadastro nº {{ str_pad($empresa->id, 6, '0', STR_PAD_LEFT) }}/2025
        </div>
    </div>

    <!-- Dados da Empresa -->
    <div class="section">
        <div class="section-header">Dados da Empresa</div>
        <div class="field-grid">
            <div class="field-group">
                <div class="field-label">Razão Social:</div>
                <div class="field-value">{{ $empresa->razao_social }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Nome Fantasia:</div>
                <div class="field-value">{{ $empresa->nome_fantasia }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">CNPJ:</div>
                <div class="field-value"><span class="cnpj-format">{{ substr($empresa->cnpj, 0, 2) }}.{{ substr($empresa->cnpj, 2, 3) }}.{{ substr($empresa->cnpj, 5, 3) }}/{{ substr($empresa->cnpj, 8, 4) }}-{{ substr($empresa->cnpj, 12, 2) }}</span></div>
            </div>

            <div class="field-group">
                <div class="field-label">Inscrição Estadual:</div>
                <div class="field-value">{{ $empresa->ie ?? '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Status:</div>
                <div class="field-value">
                    <span class="status-badge {{ $empresa->status_empresa ? 'status-active' : 'status-inactive' }}">
                        {{ $empresa->status_empresa ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Endereço -->
    <div class="section">
        <div class="section-header">Endereço</div>
        <div class="field-grid">
            <div class="field-group">
                <div class="field-label">CEP:</div>
                <div class="field-value">{{ $empresa->cep ? substr($empresa->cep, 0, 5) . '-' . substr($empresa->cep, 5) : '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Endereço:</div>
                <div class="field-value">{{ $empresa->endereco ?? '-' }}, {{ $empresa->numero ?? '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Bairro:</div>
                <div class="field-value">{{ $empresa->bairro ?? '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Cidade/UF:</div>
                <div class="field-value">{{ $empresa->cidade ?? '-' }}/{{ $empresa->uf ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Contato -->
    <div class="section">
        <div class="section-header">Informações de Contato</div>
        <div class="contact-info">
            <div class="contact-item">
                <strong>Contato Principal:</strong> {{ $empresa->nome_contato ?? '-' }}
                @if($empresa->cpf_contato)
                    (CPF: <span class="cnpj-format">{{ substr($empresa->cpf_contato, 0, 3) }}.{{ substr($empresa->cpf_contato, 3, 3) }}.{{ substr($empresa->cpf_contato, 6, 3) }}-{{ substr($empresa->cpf_contato, 9, 2) }}</span>)
                @endif
            </div>

            @if($empresa->telefone)
            <div class="contact-item">
                <span class="contact-icon">📞</span>
                <strong>Telefone:</strong> {{ $empresa->telefone }}
            </div>
            @endif

            @if($empresa->celular)
            <div class="contact-item">
                <span class="contact-icon">📱</span>
                <strong>Celular:</strong> {{ $empresa->celular }}
            </div>
            @endif

            @if($empresa->whatsapp_financeiro)
            <div class="contact-item">
                <span class="contact-icon">💬</span>
                <strong>WhatsApp Financeiro:</strong> {{ $empresa->whatsapp_financeiro }}
            </div>
            @endif

            @if($empresa->email)
            <div class="contact-item">
                <span class="contact-icon">✉️</span>
                <strong>E-mail:</strong> {{ $empresa->email }}
            </div>
            @endif

            @if($empresa->email_financeiro)
            <div class="contact-item">
                <span class="contact-icon">📧</span>
                <strong>E-mail Financeiro:</strong> {{ $empresa->email_financeiro }}
            </div>
            @endif
        </div>
    </div>

    <!-- Dados Bancários -->
    <div class="section">
        <div class="section-header">Dados Bancários</div>
        <div class="banking-section">
            <div class="banking-title">Informações para Depósito/Transferência</div>
            <div class="field-grid">
                <div class="field-group">
                    <div class="field-label">Banco:</div>
                    <div class="field-value">{{ $empresa->banco ?? '-' }}</div>
                </div>

                <div class="field-group">
                    <div class="field-label">Agência:</div>
                    <div class="field-value">{{ $empresa->agencia ?? '-' }}</div>
                </div>

                <div class="field-group">
                    <div class="field-label">Conta:</div>
                    <div class="field-value">{{ $empresa->conta ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Dados PIX -->
        <div class="banking-section">
            <div class="banking-title">Chave PIX</div>
            <div class="field-grid">
                <div class="field-group">
                    <div class="field-label">Nome do Favorecido:</div>
                    <div class="field-value">{{ $empresa->nome_favorecido_pix ?? '-' }}</div>
                </div>

                <div class="field-group">
                    <div class="field-label">Tipo de Chave:</div>
                    <div class="field-value">
                        @switch($empresa->tipo_pix)
                            @case('CNPJ') CNPJ @break
                            @case('CPF') CPF @break
                            @case('EMAIL') E-mail @break
                            @case('TELEFONE') Telefone @break
                            @case('CHAVE_ALEATORIA') Chave Aleatória @break
                            @case('AGENCIA_CONTA') Agência e Conta @break
                            @default -
                        @endswitch
                    </div>
                </div>

                <div class="field-group" style="grid-column: span 2;">
                    <div class="field-label">Chave PIX:</div>
                    <div class="field-value">{{ $empresa->chave_pix ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsáveis -->
    <div class="section">
        <div class="section-header">Responsáveis</div>
        <div class="field-grid">
            <div class="field-group">
                <div class="field-label">Operador:</div>
                <div class="field-value">{{ $empresa->operador ?? '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Supervisor:</div>
                <div class="field-value">{{ $empresa->supervisor ?? '-' }}</div>
            </div>

            <div class="field-group" style="grid-column: span 2;">
                <div class="field-label">Gerente:</div>
                <div class="field-value">{{ $empresa->gerente ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Condições Comerciais -->
    <div class="section">
        <div class="section-header">Condições Comerciais</div>
        <div class="field-grid">
            <div class="field-group" style="grid-column: span 2;">
                <div class="field-label">Valor de Adesão:</div>
                <div class="field-value highlight-value">
                    R$ {{ number_format($valorAdesao, 2, ',', '.') }}<br>
                    <small>({{ $valorExtenso }})</small>
                </div>
            </div>

            <div class="field-group">
                <div class="field-label">Plano de Remuneração:</div>
                <div class="field-value">{{ $empresa->plano ? $empresa->plano->nome : '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Parcelas Máximas:</div>
                <div class="field-value">{{ $empresa->qtd_parcelas ?? '-' }}</div>
            </div>

            @if($empresa->desconto_total_avista)
            <div class="field-group">
                <div class="field-label">Desconto à Vista:</div>
                <div class="field-value">{{ number_format($empresa->desconto_total_avista, 2, ',', '.') }}%</div>
            </div>
            @endif

            @if($empresa->desconto_total_aprazo)
            <div class="field-group">
                <div class="field-label">Desconto a Prazo:</div>
                <div class="field-value">{{ number_format($empresa->desconto_total_aprazo, 2, ',', '.') }}%</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="section">
        <div class="section-header">Informações do Sistema</div>
        <div class="field-grid">
            <div class="field-group">
                <div class="field-label">Data de Cadastro:</div>
                <div class="field-value">{{ $empresa->created_at ? $empresa->created_at->format('d/m/Y H:i') : '-' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Última Atualização:</div>
                <div class="field-value">{{ $empresa->updated_at ? $empresa->updated_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Observações -->
    <div class="observacoes-section">
        <div class="observacoes-title">Observações:</div>
        <div class="observacoes-content">
            <!-- Espaço reservado para observações adicionais -->
            ________________________________________________________________________________<br>
            ________________________________________________________________________________<br>
            ________________________________________________________________________________<br>
        </div>
    </div>

    <!-- Assinatura -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Assinatura do Responsável</div>
            <div class="signature-detail">
                {{ $empresa->nome_contato ?? $empresa->razao_social }}<br>
                {{ $empresa->cpf_contato ? 'CPF: ' . substr($empresa->cpf_contato, 0, 3) . '.' . substr($empresa->cpf_contato, 3, 3) . '.' . substr($empresa->cpf_contato, 6, 3) . '-' . substr($empresa->cpf_contato, 9, 2) : 'CNPJ: ' . substr($empresa->cnpj, 0, 2) . '.' . substr($empresa->cnpj, 2, 3) . '.' . substr($empresa->cnpj, 5, 3) . '/' . substr($empresa->cnpj, 8, 4) . '-' . substr($empresa->cnpj, 12, 2) }}
            </div>
        </div>
    </div>

    <!-- Rodapé do Documento -->
    <div class="document-footer">
        Documento gerado pelo sistema RecuperaX em {{ \Carbon\Carbon::now()->format('d/m/Y \à\s H:i:s') }}<br>
        Este documento é confidencial e destina-se exclusivamente ao uso interno da empresa.
    </div>

    <script>
        function downloadPDF() {
            // Para gerar PDF real, seria necessário usar uma biblioteca como jsPDF ou html2pdf
            // Por enquanto, vamos otimizar para impressão e sugerir salvar como PDF
            alert('Para gerar PDF: Use Ctrl+P (Windows/Linux) ou Cmd+P (Mac), selecione "Salvar como PDF" na impressora.');
            window.print();
        }

        // Otimizar layout para impressão
        function optimizeForPrint() {
            // Ajustes automáticos antes da impressão
            const style = document.createElement('style');
            style.textContent = `
                @page {
                    size: A4;
                    margin: 2.5cm 2cm 2cm 3cm;
                }

                body {
                    font-size: 10pt !important;
                }

                .print-controls {
                    display: none !important;
                }

                .field-value {
                    background: white !important;
                    -webkit-print-color-adjust: exact;
                }

                .highlight-value {
                    -webkit-print-color-adjust: exact;
                }
            `;
            document.head.appendChild(style);
        }

        // Preparar para impressão
        window.addEventListener('beforeprint', optimizeForPrint);

        // Também preparar quando o usuário pressionar Ctrl+P
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                setTimeout(optimizeForPrint, 100);
            }
        });
    </script>
</body>
</html>