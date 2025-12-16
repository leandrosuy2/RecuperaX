<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'core_empresa';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'nome_contato',
        'cpf_contato',
        'banco',
        'ie',
        'telefone',
        'celular',
        'whatsapp_financeiro',
        'operador',
        'supervisor',
        'gerente',
        'plano_id',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'uf',
        'cidade',
        'email',
        'email_financeiro',
        'valor_adesao',
        'usuario',
        'senha',
        'logo',
        'nome_favorecido_pix',
        'tipo_pix',
        'status_empresa',
    ];

    protected $casts = [
        'status_empresa' => 'boolean',
    ];

    // Relacionamentos
    public function plano(): BelongsTo
    {
        return $this->belongsTo(TabelaRemuneracao::class, 'plano_id');
    }

    public function devedores(): HasMany
    {
        return $this->hasMany(Devedor::class, 'empresa_id');
    }

    public function titulos(): HasMany
    {
        return $this->hasMany(Titulo::class, 'empresa_id');
    }

    public function acordos(): HasMany
    {
        return $this->hasMany(Acordo::class, 'empresa_id');
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'empresa_id');
    }

    public function cobrancas(): HasMany
    {
        return $this->hasMany(Cobranca::class, 'empresa_id');
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class, 'empresa_id');
    }

    public function whatsappTemplates(): HasMany
    {
        return $this->hasMany(WhatsappTemplate::class, 'empresa_id');
    }

    public function usersLojistas(): HasMany
    {
        return $this->hasMany(UsersLojistas::class, 'empresa_id');
    }
}
