<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoCobranca extends Model
{
    use HasFactory;

    protected $table = 'historico_cobranca';

    protected $fillable = [
        'divida_id',
        'cliente_id',
        'consultor_id',
        'followup_id',
        'acordo_id',
        'pagamento_id',
        'tipo_acao',
        'descricao',
        'dados_anteriores',
        'dados_novos',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    // Relacionamentos
    public function divida(): BelongsTo
    {
        return $this->belongsTo(Divida::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultor_id');
    }

    public function followup(): BelongsTo
    {
        return $this->belongsTo(Followup::class);
    }

    public function acordo(): BelongsTo
    {
        return $this->belongsTo(Acordo::class);
    }

    public function pagamento(): BelongsTo
    {
        return $this->belongsTo(Pagamento::class);
    }
}
