<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Carteira extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'credor_id',
        'consultor_id',
        'dias_atraso_min',
        'dias_atraso_max',
        'valor_min',
        'valor_max',
        'status_filtro',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'valor_min' => 'decimal:2',
        'valor_max' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function credor(): BelongsTo
    {
        return $this->belongsTo(Credor::class);
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultor_id');
    }

    public function dividas(): BelongsToMany
    {
        return $this->belongsToMany(Divida::class, 'carteira_divida');
    }

    // Métodos auxiliares
    public function aplicarFiltros($query)
    {
        if ($this->credor_id) {
            $query->where('credor_id', $this->credor_id);
        }

        if ($this->status_filtro) {
            $query->where('status', $this->status_filtro);
        }

        if ($this->dias_atraso_min || $this->dias_atraso_max) {
            $query->where(function ($q) {
                if ($this->dias_atraso_min) {
                    $q->whereRaw('DATEDIFF(CURDATE(), data_vencimento) >= ?', [$this->dias_atraso_min]);
                }
                if ($this->dias_atraso_max) {
                    $q->whereRaw('DATEDIFF(CURDATE(), data_vencimento) <= ?', [$this->dias_atraso_max]);
                }
            });
        }

        if ($this->valor_min) {
            $query->where('valor_atual', '>=', $this->valor_min);
        }

        if ($this->valor_max) {
            $query->where('valor_atual', '<=', $this->valor_max);
        }

        return $query;
    }

    public function sincronizarDividas(): void
    {
        $dividas = Divida::query();
        $dividas = $this->aplicarFiltros($dividas)->get();
        
        $this->dividas()->sync($dividas->pluck('id'));
    }
}
