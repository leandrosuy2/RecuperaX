<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'cpf',
        'cnpj',
        'email',
        'telefone',
        'celular',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function dividas(): HasMany
    {
        return $this->hasMany(Divida::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }

    public function acordos(): HasMany
    {
        return $this->hasMany(Acordo::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    public function historicoCobranca(): HasMany
    {
        return $this->hasMany(HistoricoCobranca::class);
    }

    // Métodos auxiliares
    public function getDocumentoAttribute(): ?string
    {
        return $this->cpf ?? $this->cnpj;
    }

    public function getCpfCnpjAttribute(): ?string
    {
        return $this->cpf ?? $this->cnpj;
    }

    public function getDividasAtivasCountAttribute(): int
    {
        return $this->dividas()
            ->whereIn('status', ['a_vencer', 'vencida', 'em_negociacao'])
            ->count();
    }

    public function getValorTotalEmAtrasoAttribute(): float
    {
        return $this->dividas()
            ->where('status', 'vencida')
            ->sum('valor_atual');
    }

    public function getTotalDividasAttribute(): float
    {
        return $this->dividas()
            ->whereIn('status', ['a_vencer', 'vencida', 'em_negociacao'])
            ->sum('valor_atual');
    }
}
