<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Divida extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'credor_id',
        'cliente_id',
        'consultor_id',
        'numero_documento',
        'descricao',
        'valor_original',
        'valor_atual',
        'data_vencimento',
        'data_emissao',
        'status',
        'juros_mensal',
        'multa',
        'observacoes',
    ];

    protected $casts = [
        'valor_original' => 'decimal:2',
        'valor_atual' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_emissao' => 'date',
        'juros_mensal' => 'decimal:2',
        'multa' => 'decimal:2',
    ];

    // Relacionamentos
    public function credor(): BelongsTo
    {
        return $this->belongsTo(Credor::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultor_id');
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

    public function carteiras()
    {
        return $this->belongsToMany(Carteira::class, 'carteira_divida');
    }

    // Scopes
    public function scopeVencidas($query)
    {
        return $query->where('status', 'vencida')
            ->orWhere(function ($q) {
                $q->where('status', 'a_vencer')
                  ->where('data_vencimento', '<', now());
            });
    }

    public function scopeDoConsultor($query, $consultorId)
    {
        return $query->where('consultor_id', $consultorId);
    }

    public function scopeDoCredor($query, $credorId)
    {
        return $query->where('credor_id', $credorId);
    }

    // Métodos auxiliares
    public function calcularValorAtualizado(): float
    {
        $valor = $this->valor_original;
        $diasAtraso = max(0, now()->diffInDays($this->data_vencimento, false));

        if ($diasAtraso > 0 && $this->data_vencimento < now()) {
            // Aplicar multa
            $multa = $this->multa ?? $this->credor->multa_padrao ?? 2.00;
            $valor += ($valor * $multa / 100);

            // Aplicar juros mensais
            $juros = $this->juros_mensal ?? $this->credor->juros_padrao ?? 1.00;
            $mesesAtraso = $diasAtraso / 30;
            $valor += ($valor * ($juros / 100) * $mesesAtraso);
        }

        return round($valor, 2);
    }

    public function atualizarStatus(): void
    {
        if ($this->status === 'quitada' || $this->status === 'cancelada') {
            return;
        }

        if ($this->data_vencimento < now() && $this->status === 'a_vencer') {
            $this->status = 'vencida';
            $this->valor_atual = $this->calcularValorAtualizado();
            $this->save();

            // Registrar no histórico
            $this->registrarHistorico('status_alterado', 'Dívida vencida automaticamente');
        }
    }

    public function registrarHistorico(string $tipoAcao, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null): void
    {
        HistoricoCobranca::create([
            'divida_id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'consultor_id' => $this->consultor_id,
            'tipo_acao' => $tipoAcao,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
        ]);
    }

    public function getDiasAtrasoAttribute(): int
    {
        if ($this->data_vencimento >= now()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->data_vencimento));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'a_vencer' => 'A vencer',
            'vencida' => 'Vencida',
            'em_negociacao' => 'Em negociação',
            'quitada' => 'Quitada',
            'cancelada' => 'Cancelada',
            default => ucfirst($this->status ?? 'Desconhecido'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'a_vencer' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
            'vencida' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
            'em_negociacao' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
            'quitada' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
            'cancelada' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }
}
