<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabelaRemuneracaoLista extends Model
{
    use HasFactory;

    protected $table = 'core_tabelaremuneracao_lista';

    protected $fillable = [
        'tabela_remuneracao_id',
        'de_dias',
        'ate_dias',
        'percentual_remuneracao',
    ];

    protected $casts = [
        'tabela_remuneracao_id' => 'integer',
        'de_dias' => 'integer',
        'ate_dias' => 'integer',
        'percentual_remuneracao' => 'decimal:2',
    ];

    // Relacionamentos
    public function tabelaRemuneracao(): BelongsTo
    {
        return $this->belongsTo(TabelaRemuneracao::class, 'tabela_remuneracao_id');
    }
}
