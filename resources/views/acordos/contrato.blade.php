<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato de Acordo - #{{ $acordo->id }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 40px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 18pt;
            margin: 0;
            font-weight: bold;
        }
        .partes {
            margin: 30px 0;
        }
        .parte {
            margin-bottom: 20px;
        }
        .parte h3 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .clausulas {
            margin: 30px 0;
        }
        .clausula {
            margin-bottom: 15px;
            text-align: justify;
        }
        .clausula strong {
            font-weight: bold;
        }
        .parcelas {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .assinaturas {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
        }
        .assinatura {
            text-align: center;
            width: 300px;
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 60px;
        }
        .assinatura p {
            margin: 5px 0;
        }
        .data-local {
            text-align: right;
            margin-top: 30px;
        }
        @media print {
            body {
                margin: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; background: #4F46E5; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Imprimir / Salvar PDF
        </button>
        <a href="{{ route('acordos.show', $acordo) }}" style="display: inline-block; margin-left: 10px; padding: 10px 20px; font-size: 14px; background: #6B7280; color: white; text-decoration: none; border-radius: 5px;">
            Voltar
        </a>
    </div>

    <div class="header">
        <h1>CONTRATO DE ACORDO DE PAGAMENTO</h1>
        <p>Nº {{ $acordo->id }}</p>
    </div>

    <div class="partes">
        <div class="parte">
            <h3>PARTES CONTRATANTES</h3>
            <p><strong>CREDOR:</strong> {{ $acordo->empresa ? ($acordo->empresa->razao_social ?? $acordo->empresa->nome_fantasia) : 'Não informado' }}</p>
            <p>CNPJ: {{ $acordo->empresa->cnpj ?? 'Não informado' }}</p>
            @if($acordo->empresa && $acordo->empresa->endereco)
            <p>Endereço: {{ $acordo->empresa->endereco }}, {{ $acordo->empresa->numero ?? '' }} - {{ $acordo->empresa->bairro ?? '' }}, {{ $acordo->empresa->cidade ?? '' }}/{{ $acordo->empresa->uf ?? '' }}</p>
            @endif
        </div>

        <div class="parte">
            <h3>DEVEDOR</h3>
            <p><strong>NOME:</strong> {{ $acordo->nome_devedor }}</p>
            <p>CPF/CNPJ: {{ $acordo->devedor->documento ?? 'Não informado' }}</p>
            @if($acordo->devedor && $acordo->devedor->endereco)
            <p>Endereço: {{ $acordo->devedor->endereco }}, {{ $acordo->devedor->bairro ?? '' }}, {{ $acordo->devedor->cidade ?? '' }}/{{ $acordo->devedor->uf ?? '' }}</p>
            @endif
        </div>
    </div>

    <div class="clausulas">
        <div class="clausula">
            <p><strong>CLÁUSULA 1ª - DO OBJETO:</strong></p>
            <p>O presente contrato tem por objeto a regularização de débito existente entre as partes, através de acordo de pagamento parcelado.</p>
        </div>

        <div class="clausula">
            <p><strong>CLÁUSULA 2ª - DO VALOR:</strong></p>
            <p>O valor total do acordo é de <strong>R$ {{ number_format($acordo->valor_total_negociacao, 2, ',', '.') }}</strong>, sendo:</p>
            <ul>
                <li>Entrada: R$ {{ number_format($acordo->entrada, 2, ',', '.') }}</li>
                <li>Parcelas: {{ $acordo->qtde_prc }}x de R$ {{ number_format($acordo->valor_por_parcela, 2, ',', '.') }}</li>
            </ul>
        </div>

        <div class="clausula">
            <p><strong>CLÁUSULA 3ª - DAS PARCELAS:</strong></p>
            <p>O pagamento será efetuado em {{ $acordo->qtde_prc }} parcelas mensais e sucessivas, conforme tabela abaixo:</p>
            
            <div class="parcelas">
                <table>
                    <thead>
                        <tr>
                            <th>Parcela</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acordo->parcelas as $parcela)
                        <tr>
                            <td>{{ $parcela->parcela_numero }}ª</td>
                            <td>{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="clausula">
            <p><strong>CLÁUSULA 4ª - DO PAGAMENTO:</strong></p>
            <p>O pagamento das parcelas deverá ser efetuado até a data de vencimento, através dos meios de pagamento aceitos pelo credor.</p>
        </div>

        <div class="clausula">
            <p><strong>CLÁUSULA 5ª - DA MORA:</strong></p>
            <p>Em caso de atraso no pagamento de qualquer parcela, o devedor ficará sujeito aos juros e multa previstos em lei.</p>
        </div>

        <div class="clausula">
            <p><strong>CLÁUSULA 6ª - DA RESCISÃO:</strong></p>
            <p>O descumprimento de qualquer cláusula deste acordo implicará na rescisão automática do mesmo, retornando o débito ao status original.</p>
        </div>
    </div>

    <div class="data-local">
        <p>{{ now()->format('d') }} de {{ now()->locale('pt_BR')->monthName }} de {{ now()->format('Y') }}</p>
    </div>

    <div class="assinaturas">
        <div class="assinatura">
            <p>_________________________________</p>
            <p><strong>{{ $acordo->empresa ? ($acordo->empresa->razao_social ?? $acordo->empresa->nome_fantasia) : 'CREDOR' }}</strong></p>
            <p>CNPJ: {{ $acordo->empresa->cnpj ?? '' }}</p>
        </div>

        <div class="assinatura">
            <p>_________________________________</p>
            <p><strong>{{ $acordo->nome_devedor }}</strong></p>
            <p>CPF/CNPJ: {{ $acordo->devedor->documento ?? '' }}</p>
        </div>
    </div>
</body>
</html>
