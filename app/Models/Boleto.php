<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boleto extends Model
{
    use HasFactory;

    protected $table = 'core_boleto';

    protected $fillable = [
        'empresa_id',
        'codigo_solicitacao',
        'seu_numero',
        'situacao',
        'data_situacao',
        'data_emissao',
        'data_vencimento',
        'valor_nominal',
        'valor_total_recebido',
        'origem_recebimento',
        'tipo_cobranca',
        'pagador_nome',
        'pagador_cpf_cnpj',
        'nosso_numero',
        'linha_digitavel',
        'codigo_barras',
        'pix_copia_e_cola',
        'txid',
        'cobranca_enviada_whatsapp',
        'atualizado_em',
    ];

    protected $casts = [
        'empresa_id' => 'integer',
        'data_situacao' => 'date',
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'valor_nominal' => 'decimal:2',
        'valor_total_recebido' => 'decimal:2',
        'atualizado_em' => 'datetime',
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id')->withDefault();
    }
}
