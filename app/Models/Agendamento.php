<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'core_agendamento';

    protected $fillable = [
        'devedor_id',
        'empresa_id',
        'data_abertura',
        'data_retorno',
        'assunto',
        'acordo_id',
        'operador',
        'telefone',
        'status',
    ];

    protected $casts = [
        'data_abertura' => 'datetime',
        'data_retorno' => 'datetime',
        'acordo_id' => 'integer',
    ];

    // Relacionamentos
    public function devedor(): BelongsTo
    {
        return $this->belongsTo(Devedor::class, 'devedor_id')->withDefault();
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id')->withDefault();
    }

    // Métodos auxiliares
    public function isPendente(): bool
    {
        return $this->status === 'Pendente';
    }

    public function isFinalizado(): bool
    {
        return $this->status === 'Finalizado';
    }
}
