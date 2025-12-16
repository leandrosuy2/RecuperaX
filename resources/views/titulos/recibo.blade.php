<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo - Título #{{ $titulo->num_titulo }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.8;
            margin: 40px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 20pt;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .recibo-box {
            border: 2px solid #000;
            padding: 30px;
            margin: 30px 0;
        }
        .recibo-text {
            text-align: justify;
            margin: 20px 0;
            font-size: 13pt;
        }
        .valor-extenso {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14pt;
        }
        .info-section {
            margin: 25px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #000;
        }
        .info-section p {
            margin: 8px 0;
        }
        .assinatura {
            margin-top: 60px;
            text-align: center;
        }
        .assinatura-line {
            border-top: 2px solid #000;
            width: 400px;
            margin: 60px auto 10px;
            padding-top: 10px;
        }
        .data-local {
            text-align: right;
            margin-top: 30px;
            font-size: 11pt;
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
        <a href="{{ route('titulos.show', $titulo) }}" style="display: inline-block; margin-left: 10px; padding: 10px 20px; font-size: 14px; background: #6B7280; color: white; text-decoration: none; border-radius: 5px;">
            Voltar
        </a>
    </div>

    <div class="header">
        <h1>Recibo de Pagamento</h1>
    </div>

    <div class="recibo-box">
        <div class="recibo-text">
            <p>
                Recebi de <strong>{{ $titulo->devedor ? $titulo->devedor->nome_completo : 'Não informado' }}</strong>,
                CPF/CNPJ: <strong>{{ $titulo->devedor ? $titulo->devedor->documento : 'Não informado' }}</strong>,
                a quantia de <span class="valor-extenso">R$ {{ number_format($titulo->valorRecebido ?? $titulo->valor, 2, ',', '.') }}</span>
                @php
                    try {
                        $valorExtenso = \App\Helpers\NumberToWords::toWords($titulo->valorRecebido ?? $titulo->valor);
                    } catch (\Exception $e) {
                        $valorExtenso = 'valor em reais';
                    }
                @endphp
                ({{ $valorExtenso }}),
                referente ao pagamento do Título nº <strong>{{ $titulo->num_titulo }}</strong>,
                vencido em {{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : 'data não informada' }}.
            </p>
        </div>

        <div class="info-section">
            <p><strong>Empresa Credora:</strong> {{ $titulo->empresa ? ($titulo->empresa->razao_social ?? $titulo->empresa->nome_fantasia) : 'Não informado' }}</p>
            <p><strong>CNPJ:</strong> {{ $titulo->empresa->cnpj ?? 'Não informado' }}</p>
            <p><strong>Valor Original:</strong> R$ {{ number_format($titulo->valor, 2, ',', '.') }}</p>
            @if($titulo->juros > 0)
            <p><strong>Juros:</strong> R$ {{ number_format($titulo->juros, 2, ',', '.') }}</p>
            @endif
            <p><strong>Valor Pago:</strong> R$ {{ number_format($titulo->valorRecebido ?? $titulo->valor, 2, ',', '.') }}</p>
            <p><strong>Data do Pagamento:</strong> {{ $titulo->data_baixa ? $titulo->data_baixa->format('d/m/Y') : now()->format('d/m/Y') }}</p>
        </div>

        <div class="recibo-text">
            <p>Dando por quitado e pago o referido débito, não mais havendo nada a reclamar a respeito.</p>
        </div>
    </div>

    <div class="data-local">
        <p>{{ now()->format('d') }} de {{ now()->locale('pt_BR')->monthName }} de {{ now()->format('Y') }}</p>
    </div>

    <div class="assinatura">
        <div class="assinatura-line">
            <p><strong>{{ $titulo->empresa ? ($titulo->empresa->razao_social ?? $titulo->empresa->nome_fantasia) : 'CREDOR' }}</strong></p>
            <p>CNPJ: {{ $titulo->empresa->cnpj ?? '' }}</p>
        </div>
    </div>
</body>
</html>
