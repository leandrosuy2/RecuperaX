<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Acordo extends Model
{
    use HasFactory;

    protected $table = 'core_acordo';

    protected $fillable = [
        'empresa_id',
        'devedor_id',
        'titulo_id',
        'entrada',
        'data_entrada',
        'qtde_prc',
        'valor_total_negociacao',
        'diferenca_dias',
        'data_baixa',
        'venc_primeira_parcela',
        'valor_por_parcela',
        'contato',
        'tipo_doc_id',
        'forma_pag_Id',
    ];

    protected $casts = [
        'entrada' => 'decimal:2',
        'data_entrada' => 'date',
        'qtde_prc' => 'integer',
        'valor_total_negociacao' => 'decimal:2',
        'diferenca_dias' => 'integer',
        'data_baixa' => 'date',
        'venc_primeira_parcela' => 'date',
        'valor_por_parcela' => 'decimal:2',
        'tipo_doc_id' => 'integer',
        'forma_pag_Id' => 'integer',
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id')->withDefault();
    }

    public function devedor(): BelongsTo
    {
        return $this->belongsTo(Devedor::class, 'devedor_id')->withDefault();
    }

    public function titulo(): BelongsTo
    {
        return $this->belongsTo(Titulo::class, 'titulo_id')->withDefault();
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcelamento::class, 'acordo_id');
    }

    // Propriedades calculadas
    public function getNomeDevedorAttribute(): string
    {
        if ($this->devedor) {
            return $this->devedor->nome ?? $this->devedor->razao_social ?? '';
        }
        return '';
    }
}
