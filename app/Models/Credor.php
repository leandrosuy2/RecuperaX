<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credores';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'logo',
        'nome_contato',
        'cpf_contato',
        'email',
        'email_financeiro',
        'telefone',
        'celular',
        'whatsapp_financeiro',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'banco',
        'agencia',
        'conta',
        'chave_pix',
        'nome_favorecido_pix',
        'tipo_chave_pix',
        'inscricao_estadual',
        'operador',
        'supervisor',
        'gerente',
        'juros_padrao',
        'multa_padrao',
        'desconto_maximo',
        'implantacao',
        'quantidade_parcelas',
        'desconto_a_vista',
        'desconto_a_prazo',
        'plano',
        'ativo',
    ];

    protected $casts = [
        'juros_padrao' => 'decimal:2',
        'multa_padrao' => 'decimal:2',
        'desconto_maximo' => 'decimal:2',
        'implantacao' => 'decimal:2',
        'desconto_a_vista' => 'decimal:2',
        'desconto_a_prazo' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function dividas(): HasMany
    {
        return $this->hasMany(Divida::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function carteiras(): HasMany
    {
        return $this->hasMany(Carteira::class);
    }

    // Métodos auxiliares
    public function getTotalDividasAtivasAttribute(): int
    {
        return $this->dividas()->whereIn('status', ['a_vencer', 'vencida', 'em_negociacao'])->count();
    }

    public function getTotalEmAtrasoAttribute(): float
    {
        return $this->dividas()
            ->where('status', 'vencida')
            ->sum('valor_atual');
    }

    public function getTotalRecuperadoAttribute(): float
    {
        return $this->dividas()
            ->where('status', 'quitada')
            ->sum('valor_atual');
    }
}
