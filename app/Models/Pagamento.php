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
        'picpay_reference_id',
        'picpay_authorization_id',
        'picpay_payment_url',
        'picpay_qrcode_base64',
        'picpay_response',
        'picpay_expires_at',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
        'data_recebimento' => 'date',
        'picpay_response' => 'array',
        'picpay_expires_at' => 'datetime',
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

    /**
     * Verifica se é um pagamento via PicPay
     */
    public function isPicPay(): bool
    {
        return !empty($this->picpay_reference_id);
    }

    /**
     * Verifica se o pagamento PicPay está expirado
     */
    public function isPicPayExpirado(): bool
    {
        if (!$this->isPicPay() || !$this->picpay_expires_at) {
            return false;
        }

        return $this->picpay_expires_at->isPast();
    }

    /**
     * Verifica se o pagamento PicPay está pendente
     */
    public function isPicPayPendente(): bool
    {
        return $this->isPicPay() && $this->status === 'pendente' && !$this->isPicPayExpirado();
    }
}
