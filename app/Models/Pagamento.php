<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'divida_id',
        'acordo_id',
        'cliente_id',
        'consultor_id',
        'numero_transacao',
        'valor',
        'data_pagamento',
        'data_recebimento',
        'forma_pagamento',
        'status',
        'numero_parcela',
        'data_vencimento_parcela',
        'observacoes',
        'comprovante',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
        'data_recebimento' => 'date',
    ];

    // Relacionamentos
    public function divida(): BelongsTo
    {
        return $this->belongsTo(Divida::class);
    }

    public function acordo(): BelongsTo
    {
        return $this->belongsTo(Acordo::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultor_id');
    }

    public function historicoCobranca(): BelongsTo
    {
        return $this->belongsTo(HistoricoCobranca::class);
    }

    // Métodos auxiliares
    public function confirmar(): void
    {
        $this->status = 'confirmado';
        if (!$this->data_recebimento) {
            $this->data_recebimento = now();
        }
        $this->save();

        // Atualizar acordo se existir
        if ($this->acordo) {
            if ($this->acordo->isQuitado()) {
                $this->acordo->status = 'quitado';
                $this->acordo->save();

                // Atualizar dívida
                $this->divida->status = 'quitada';
                $this->divida->save();
            }
        } else {
            // Verificar se quitou a dívida diretamente
            $valorPago = $this->divida->pagamentos()
                ->where('status', 'confirmado')
                ->sum('valor');
            
            if ($valorPago >= $this->divida->valor_atual) {
                $this->divida->status = 'quitada';
                $this->divida->save();
            }
        }

        // Registrar no histórico
        $this->divida->registrarHistorico(
            'pagamento_recebido',
            "Pagamento de R$ " . number_format($this->valor, 2, ',', '.') . " confirmado",
            ['status' => 'pendente'],
            ['status' => 'confirmado', 'valor' => $this->valor]
        );
    }
}
