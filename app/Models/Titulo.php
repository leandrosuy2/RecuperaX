<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Titulo extends Model
{
    use HasFactory;

    protected $table = 'titulo';

    protected $fillable = [
        'devedor_id',
        'empresa_id',
        'idTituloRef',
        'num_titulo',
        'tipo_doc_id',
        'dataEmissao',
        'dataVencimento',
        'dataVencimentoReal',
        'dataVencimentoPrimeira',
        'data_baixa',
        'primeiro_vencimento',
        'valor',
        'juros',
        'valorRecebido',
        'total_parcelamento',
        'total_acordo',
        'parcelar_valor',
        'qtde_parcelas',
        'nPrc',
        'dias_atraso',
        'intervalo_dias',
        'forma_pag_Id',
        'statusBaixa',
        'statusBaixaGeral',
        'acordoComfirmed',
        'id_cobranca',
        'email_enviado',
        'data_envio_whatsapp',
        'telefone_enviado',
        'ultima_acao',
        'comprovante',
        'contrato',
        'operador',
    ];

    protected $casts = [
        'dataEmissao' => 'date',
        'dataVencimento' => 'date',
        'dataVencimentoReal' => 'date',
        'dataVencimentoPrimeira' => 'date',
        'data_baixa' => 'date',
        'primeiro_vencimento' => 'date',
        'data_envio_whatsapp' => 'date',
        'ultima_acao' => 'date',
        'valor' => 'decimal:2',
        'juros' => 'decimal:2',
        'valorRecebido' => 'decimal:2',
        'total_parcelamento' => 'decimal:2',
        'total_acordo' => 'decimal:2',
        'parcelar_valor' => 'decimal:2',
        'statusBaixa' => 'integer',
        'statusBaixaGeral' => 'integer',
        'acordoComfirmed' => 'boolean',
        'qtde_parcelas' => 'integer',
        'nPrc' => 'integer',
        'dias_atraso' => 'integer',
        'intervalo_dias' => 'integer',
        'forma_pag_Id' => 'integer',
        'tipo_doc_id' => 'integer',
        'idTituloRef' => 'integer',
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

    public function tipoDoc(): BelongsTo
    {
        return $this->belongsTo(TipoDocTitulo::class, 'tipo_doc_id')->withDefault();
    }

    public function acordos(): HasMany
    {
        return $this->hasMany(Acordo::class, 'titulo_id');
    }

    // Propriedades calculadas
    public function getValorComJurosAttribute(): float
    {
        return round($this->valor + $this->juros, 2);
    }

    public function isPendente(): bool
    {
        return $this->statusBaixa === 0 || $this->statusBaixa === null;
    }

    public function isQuitado(): bool
    {
        return $this->statusBaixa === 2;
    }

    public function isNegociado(): bool
    {
        return $this->statusBaixa === 3;
    }
}
