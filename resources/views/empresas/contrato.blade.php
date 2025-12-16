<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Lojista - {{ $empresa->nome_fantasia }}</title>
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
            font-size: 12pt;
            line-height: 1.6;
            color: #2c3e50;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .document-header {
            position: relative;
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 3px double #3498db;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }

        .company-info {
            text-align: center;
        }

        .main-title {
            font-size: 22pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 14pt;
            color: #7f8c8d;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .contract-number {
            font-size: 10pt;
            color: #95a5a6;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 25px;
            text-align: justify;
        }

        .section-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 8px 15px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-content {
            padding: 0 10px;
            text-align: justify;
            hyphens: auto;
            word-wrap: break-word;
        }

        .party-info {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 5px 5px 0;
        }

        .party-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 10pt;
            letter-spacing: 0.5px;
        }

        .party-detail {
            margin-bottom: 4px;
            line-height: 1.5;
        }

        .contract-value {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(255,193,7,0.2);
        }

        .value-amount {
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .value-extenso {
            font-size: 11pt;
            color: #7f8c8d;
            font-style: italic;
        }

        .contract-text {
            text-align: justify;
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .clause-list {
            margin-left: 20px;
        }

        .clause-item {
            margin-bottom: 8px;
            padding-left: 5px;
            position: relative;
        }

        .clause-item::before {
            content: "•";
            color: #3498db;
            font-weight: bold;
            position: absolute;
            left: -15px;
        }

        .signature-section {
            margin-top: 60px;
            page-break-inside: avoid;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
            margin: 0 20px;
            padding-top: 40px;
            border-top: 2px solid #2c3e50;
            position: relative;
        }

        .signature-box::before {
            content: "_______________________________";
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #95a5a6;
            font-size: 10pt;
        }

        .signature-title {
            font-weight: bold;
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .signature-detail {
            font-size: 10pt;
            color: #7f8c8d;
            line-height: 1.4;
        }

        .date-location {
            text-align: right;
            margin-top: 40px;
            font-size: 11pt;
            color: #2c3e50;
            padding-right: 50px;
        }

        .cnpj-format {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11pt;
        }

        .highlight-box {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 0 5px 5px 0;
            font-weight: 500;
        }

        .footer-note {
            font-size: 9pt;
            color: #95a5a6;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ecf0f1;
            padding-top: 10px;
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
                font-size: 11pt;
            }

            .document-header {
                margin-bottom: 30px;
            }

            .signature-section {
                page-break-inside: avoid;
                margin-top: 40px;
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
        <button onclick="window.print()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors" title="Imprimir Contrato">
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
        <div class="main-title">Contrato de Prestação de Serviços</div>
        <div class="contract-number">
            Contrato nº {{ str_pad($empresa->id, 6, '0', STR_PAD_LEFT) }}/2025
        </div>
    </div>

    <!-- Identificação das Partes -->
    <div class="section">
        <div class="section-header">1. Identificação das Partes</div>
        <div class="section-content">
            <div class="party-info">
                <div class="party-label">Contratado (Sistema RecuperaX)</div>
                <div class="party-detail"><strong>RecuperaX Sistema de Cobrança LTDA</strong></div>
                <div class="party-detail">CNPJ: <span class="cnpj-format">12.345.678/0001-90</span></div>
                <div class="party-detail">Endereço: Rua das Tecnologias, 123 - Centro - São Paulo/SP</div>
            </div>

            <div class="party-info">
                <div class="party-label">Contratante</div>
                <div class="party-detail"><strong>{{ $empresa->razao_social }}</strong></div>
                <div class="party-detail">Nome Fantasia: {{ $empresa->nome_fantasia }}</div>
                <div class="party-detail">CNPJ: <span class="cnpj-format">{{ substr($empresa->cnpj, 0, 2) }}.{{ substr($empresa->cnpj, 2, 3) }}.{{ substr($empresa->cnpj, 5, 3) }}/{{ substr($empresa->cnpj, 8, 4) }}-{{ substr($empresa->cnpj, 12, 2) }}</span></div>
                <div class="party-detail">Endereço: {{ $empresa->endereco }}, {{ $empresa->numero }} - {{ $empresa->bairro }} - {{ $empresa->cidade }}/{{ $empresa->uf }}</div>
                @if($empresa->telefone)
                    <div class="party-detail">Telefone: {{ $empresa->telefone }}</div>
                @endif
                @if($empresa->email)
                    <div class="party-detail">E-mail: {{ $empresa->email }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Objeto do Contrato -->
    <div class="section">
        <div class="section-header">2. Objeto do Contrato</div>
        <div class="section-content">
            <p class="contract-text">
                Pelo presente instrumento particular de prestação de serviços, as partes acima qualificadas têm entre si justo e contratado o que segue:
            </p>
            <p class="contract-text">
                O <strong>CONTRATADO</strong> compromete-se a fornecer ao <strong>CONTRATANTE</strong> o acesso e utilização do sistema informatizado <strong>RecuperaX</strong>, plataforma especializada em cobrança e recuperação de créditos, mediante as condições estabelecidas neste instrumento.
            </p>
            <div class="highlight-box">
                O sistema inclui ferramentas de gestão de devedores, geração de boletos, relatórios analíticos e suporte técnico especializado.
            </div>
        </div>
    </div>

    <!-- Valor do Contrato -->
    <div class="section">
        <div class="section-header">3. Valor e Condições de Pagamento</div>
        <div class="section-content">
            <p class="contract-text">
                O valor de adesão e implantação do sistema RecuperaX é estabelecido em:
            </p>

            <div class="contract-value">
                <div class="value-amount">R$ {{ number_format($valorAdesao, 2, ',', '.') }}</div>
                <div class="value-extenso">({{ $valorExtenso }})</div>
            </div>

            <p class="contract-text">
                O pagamento da taxa de adesão deverá ser realizado em até 05 (cinco) dias úteis após a assinatura deste contrato, mediante boleto bancário ou transferência bancária para a conta do CONTRATADO.
            </p>
        </div>
    </div>

    <!-- Tabela de Remuneração -->
    @if($empresa->plano)
    <div class="section">
        <div class="section-header">4. Tabela de Remuneração</div>
        <div class="section-content">
            <p class="contract-text">
                O CONTRATANTE utilizará a seguinte tabela de remuneração para negociação e cobrança dos devedores cadastrados no sistema:
            </p>

            <div class="highlight-box">
                <strong>{{ $empresa->plano->nome }}</strong>
                @if($empresa->plano->descricao)
                    <br>{{ $empresa->plano->descricao }}
                @endif
            </div>

            <p class="contract-text">
                A remuneração será calculada automaticamente pelo sistema conforme os parâmetros estabelecidos na tabela selecionada.
            </p>
        </div>
    </div>
    @endif

    <!-- Prazo e Vigência -->
    <div class="section">
        <div class="section-header">5. Prazo e Vigência</div>
        <div class="section-content">
            <p class="contract-text">
                O presente contrato terá vigência por prazo indeterminado, iniciando-se na data de sua assinatura e permanecendo em vigor até que seja rescindido por qualquer das partes.
            </p>
            <p class="contract-text">
                A rescisão poderá ser feita mediante aviso prévio de 30 (trinta) dias, por escrito, sem incidência de multa rescisória.
            </p>
        </div>
    </div>

    <!-- Obrigações das Partes -->
    <div class="section">
        <div class="section-header">6. Obrigações das Partes</div>
        <div class="section-content">
            <p><strong>6.1 Obrigações do CONTRATADO:</strong></p>
            <div class="clause-list">
                <div class="clause-item">Fornecer o sistema RecuperaX em pleno funcionamento e disponível 99% do tempo;</div>
                <div class="clause-item">Prestar suporte técnico qualificado aos usuários do CONTRATANTE;</div>
                <div class="clause-item">Manter a confidencialidade e segurança dos dados dos devedores;</div>
                <div class="clause-item">Cumprir com a legislação vigente sobre proteção de dados (LGPD);</div>
                <div class="clause-item">Realizar backups diários e manter redundância dos dados;</div>
                <div class="clause-item">Atualizar o sistema com correções de bugs e melhorias;</div>
            </div>

            <p style="margin-top: 20px;"><strong>6.2 Obrigações do CONTRATANTE:</strong></p>
            <div class="clause-list">
                <div class="clause-item">Utilizar o sistema exclusivamente para fins legais e éticos;</div>
                <div class="clause-item">Manter atualizados e corretos os dados dos devedores;</div>
                <div class="clause-item">Realizar o pagamento das taxas de adesão e mensalidades;</div>
                <div class="clause-item">Não compartilhar dados de acesso com terceiros;</div>
                <div class="clause-item">Comunicar imediatamente qualquer irregularidade no sistema;</div>
                <div class="clause-item">Seguir as boas práticas de cobrança estabelecidas;</div>
            </div>
        </div>
    </div>

    <!-- Responsabilidades -->
    <div class="section">
        <div class="section-header">7. Responsabilidades e Limitações</div>
        <div class="section-content">
            <p class="contract-text">
                O CONTRATADO não se responsabiliza por perdas ou danos decorrentes de:
            </p>
            <div class="clause-list">
                <div class="clause-item">Utilização inadequada do sistema pelo CONTRATANTE;</div>
                <div class="clause-item">Dados incorretos ou incompletos fornecidos pelo CONTRATANTE;</div>
                <div class="clause-item">Falhas na infraestrutura de internet do CONTRATANTE;</div>
                <div class="clause-item">Problemas decorrentes de força maior ou caso fortuito;</div>
            </div>
        </div>
    </div>

    <!-- Foro -->
    <div class="section">
        <div class="section-header">8. Foro e Legislação Aplicável</div>
        <div class="section-content">
            <p class="contract-text">
                Para dirimir quaisquer dúvidas oriundas deste contrato, fica eleito o foro da Comarca de <strong>{{ $empresa->cidade }}/{{ $empresa->uf }}</strong>, renunciando as partes a qualquer outro, por mais privilegiado que seja.
            </p>
            <p class="contract-text">
                Este contrato será regido pelas leis da República Federativa do Brasil.
            </p>
        </div>
    </div>

    <!-- Disposições Finais -->
    <div class="section">
        <div class="section-header">9. Disposições Finais</div>
        <div class="section-content">
            <p class="contract-text">
                Este contrato representa a totalidade do acordo entre as partes, substituindo todos os entendimentos anteriores, sejam escritos ou verbais.
            </p>
            <p class="contract-text">
                Qualquer alteração ou aditamento a este contrato deverá ser feito por escrito e assinado por ambas as partes.
            </p>
        </div>
    </div>

    <!-- Local e Data -->
    <div class="date-location">
        {{ $empresa->cidade }}/{{ $empresa->uf }}, {{ \Carbon\Carbon::now()->format('d') }} de {{ \Carbon\Carbon::now()->format('F') }} de {{ \Carbon\Carbon::now()->format('Y') }}
    </div>

    <!-- Assinaturas -->
    <div class="signature-section">
        <div class="signature-container">
            <div class="signature-box">
                <div class="signature-title">CONTRATADO</div>
                <div class="signature-detail">
                    RecuperaX Sistema de Cobrança LTDA<br>
                    CNPJ: 12.345.678/0001-90
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-title">CONTRATANTE</div>
                <div class="signature-detail">
                    {{ $empresa->razao_social }}<br>
                    CNPJ: {{ substr($empresa->cnpj, 0, 2) }}.{{ substr($empresa->cnpj, 2, 3) }}.{{ substr($empresa->cnpj, 5, 3) }}/{{ substr($empresa->cnpj, 8, 4) }}-{{ substr($empresa->cnpj, 12, 2) }}<br>
                    @if($empresa->nome_contato)
                        Representante: {{ $empresa->nome_contato }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Nota de Rodapé -->
    <div class="footer-note">
        Este documento foi gerado eletronicamente pelo sistema RecuperaX em {{ \Carbon\Carbon::now()->format('d/m/Y \à\s H:i') }}
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
                    font-size: 11pt !important;
                }

                .print-controls {
                    display: none !important;
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