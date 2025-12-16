<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TabelaRemuneracao extends Model
{
    use HasFactory;

    protected $table = 'core_tabelaremuneracao';

    protected $fillable = [
        'nome',
    ];

    // Relacionamentos
    public function itens(): HasMany
    {
        return $this->hasMany(TabelaRemuneracaoLista::class, 'tabela_remuneracao_id');
    }

    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class, 'plano_id');
    }
}
