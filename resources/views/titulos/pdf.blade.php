<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título #{{ $titulo->num_titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
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
            font-size: 16pt;
            margin: 0;
            font-weight: bold;
        }
        .info-box {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dotted #ccc;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            width: 200px;
        }
        .value {
            flex: 1;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status.pendente {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status.quitado {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status.negociado {
            background-color: #DBEAFE;
            color: #1E40AF;
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
        <h1>TÍTULO DE DÉBITO</h1>
        <p>Nº {{ $titulo->num_titulo }}</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="label">Devedor:</span>
            <span class="value">{{ $titulo->devedor ? $titulo->devedor->nome_completo : 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">CPF/CNPJ:</span>
            <span class="value">{{ $titulo->devedor ? $titulo->devedor->documento : 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Empresa Credora:</span>
            <span class="value">{{ $titulo->empresa ? ($titulo->empresa->razao_social ?? $titulo->empresa->nome_fantasia) : 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">CNPJ:</span>
            <span class="value">{{ $titulo->empresa->cnpj ?? 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Data de Emissão:</span>
            <span class="value">{{ $titulo->dataEmissao ? $titulo->dataEmissao->format('d/m/Y') : 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Data de Vencimento:</span>
            <span class="value">{{ $titulo->dataVencimento ? $titulo->dataVencimento->format('d/m/Y') : 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Valor Original:</span>
            <span class="value">R$ {{ number_format($titulo->valor, 2, ',', '.') }}</span>
        </div>
        @if($titulo->juros > 0)
        <div class="info-row">
            <span class="label">Juros:</span>
            <span class="value">R$ {{ number_format($titulo->juros, 2, ',', '.') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="label">Valor Total:</span>
            <span class="value"><strong>R$ {{ number_format($titulo->valor_com_juros, 2, ',', '.') }}</strong></span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            <span class="value">
                @if($titulo->statusBaixa === 0 || $titulo->statusBaixa === null)
                    <span class="status pendente">PENDENTE</span>
                @elseif($titulo->statusBaixa === 2)
                    <span class="status quitado">QUITADO</span>
                @elseif($titulo->statusBaixa === 3)
                    <span class="status negociado">NEGOCIADO</span>
                @endif
            </span>
        </div>
        @if($titulo->valorRecebido > 0)
        <div class="info-row">
            <span class="label">Valor Recebido:</span>
            <span class="value">R$ {{ number_format($titulo->valorRecebido, 2, ',', '.') }}</span>
        </div>
        @endif
        @if($titulo->data_baixa)
        <div class="info-row">
            <span class="label">Data de Baixa:</span>
            <span class="value">{{ $titulo->data_baixa->format('d/m/Y') }}</span>
        </div>
        @endif
        @if($titulo->operador)
        <div class="info-row">
            <span class="label">Operador:</span>
            <span class="value">{{ $titulo->operador }}</span>
        </div>
        @endif
    </div>

    @if($titulo->acordos->count() > 0)
    <div style="margin-top: 30px;">
        <h2 style="font-size: 14pt; margin-bottom: 15px;">Acordos Relacionados</h2>
        @foreach($titulo->acordos as $acordo)
        <div class="info-box" style="margin-bottom: 15px;">
            <div class="info-row">
                <span class="label">Acordo #{{ $acordo->id }}:</span>
                <span class="value">{{ $acordo->qtde_prc }}x de R$ {{ number_format($acordo->valor_por_parcela, 2, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Valor Total:</span>
                <span class="value">R$ {{ number_format($acordo->valor_total_negociacao, 2, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</body>
</html>
