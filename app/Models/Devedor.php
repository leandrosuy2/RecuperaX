<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devedor extends Model
{
    use HasFactory;

    protected $table = 'devedores';

    protected $fillable = [
        'empresa_id',
        'tipo_pessoa',
        'cpf',
        'cnpj',
        'nome',
        'razao_social',
        'nome_fantasia',
        'nome_mae',
        'rg',
        'nome_socio',
        'cpf_socio',
        'rg_socio',
        'telefone',
        'telefone_valido',
        'telefone2',
        'telefone2_valido',
        'telefone3',
        'telefone3_valido',
        'telefone4',
        'telefone4_valido',
        'telefone5',
        'telefone5_valido',
        'telefone6',
        'telefone6_valido',
        'telefone7',
        'telefone7_valido',
        'telefone8',
        'telefone8_valido',
        'telefone9',
        'telefone9_valido',
        'telefone10',
        'telefone10_valido',
        'email1',
        'email2',
        'cep',
        'endereco',
        'bairro',
        'uf',
        'cidade',
        'observacao',
        'operadora',
        'module',
        'status_code',
    ];

    protected $casts = [
        'status_code' => 'integer',
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id')->withDefault();
    }

    public function titulos(): HasMany
    {
        return $this->hasMany(Titulo::class, 'devedor_id');
    }

    public function acordos(): HasMany
    {
        return $this->hasMany(Acordo::class, 'devedor_id');
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'devedor_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'devedor_id');
    }

    // Métodos auxiliares
    public function getDocumentoAttribute(): ?string
    {
        return $this->cpf ?? $this->cnpj;
    }

    public function getNomeCompletoAttribute(): string
    {
        if ($this->tipo_pessoa === 'F') {
            return $this->nome ?? '';
        } else {
            return $this->razao_social ?? $this->nome_fantasia ?? '';
        }
    }
    
    public function getCpfCnpjAttribute(): ?string
    {
        return $this->cpf ?? $this->cnpj;
    }

    public function getTelefonesAttribute(): array
    {
        $telefones = [];
        for ($i = 1; $i <= 10; $i++) {
            $telefone = $this->{"telefone{$i}"};
            if ($telefone) {
                $telefones[] = [
                    'numero' => $telefone,
                    'valido' => $this->{"telefone{$i}_valido"} ?? 'NAO VERIFICADO',
                ];
            }
        }
        return $telefones;
    }
}
