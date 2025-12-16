<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcelamento extends Model
{
    use HasFactory;

    protected $table = 'core_parcelamento';

    protected $fillable = [
        'acordo_id',
        'parcela_numero',
        'data_vencimento',
        'data_vencimento_parcela',
        'data_baixa',
        'valor',
        'comprovante',
        'status',
        'forma_pagamento',
    ];

    protected $casts = [
        'parcela_numero' => 'integer',
        'data_vencimento' => 'date',
        'data_vencimento_parcela' => 'date',
        'data_baixa' => 'date',
        'valor' => 'decimal:2',
    ];

    // Relacionamentos
    public function acordo(): BelongsTo
    {
        return $this->belongsTo(Acordo::class, 'acordo_id')->withDefault();
    }

    // Métodos auxiliares
    public function isPendente(): bool
    {
        return $this->status === 'PENDENTE';
    }

    public function isPago(): bool
    {
        return $this->status === 'PAGO';
    }
}
