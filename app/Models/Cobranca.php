<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cobranca extends Model
{
    use HasFactory;

    protected $table = 'core_cobranca';

    protected $fillable = [
        'empresa_id',
        'data_cobranca',
        'valor_comissao',
        'pago',
        'tipo_anexo',
        'documento',
        'link',
    ];

    protected $casts = [
        'data_cobranca' => 'date',
        'valor_comissao' => 'decimal:2',
        'pago' => 'boolean',
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id')->withDefault();
    }
}
