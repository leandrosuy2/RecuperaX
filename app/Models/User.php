<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'django_password',
        'role',
        'credor_id',
        'is_superuser',
        'is_staff',
        'is_active',
        'last_login',
        'date_joined',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_superuser' => 'boolean',
            'is_staff' => 'boolean',
            'is_active' => 'boolean',
            'last_login' => 'datetime',
            'date_joined' => 'datetime',
        ];
    }

    // Relacionamentos
    public function credor(): BelongsTo
    {
        return $this->belongsTo(Credor::class);
    }

    public function dividas(): HasMany
    {
        return $this->hasMany(Divida::class, 'consultor_id');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class, 'consultor_id');
    }

    public function carteiras(): HasMany
    {
        return $this->hasMany(Carteira::class, 'consultor_id');
    }

    public function acordos(): HasMany
    {
        return $this->hasMany(Acordo::class, 'consultor_id');
    }

    public function historicoCobranca(): HasMany
    {
        return $this->hasMany(HistoricoCobranca::class, 'consultor_id');
    }

    // Métodos auxiliares
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGestor(): bool
    {
        return $this->role === 'gestor';
    }

    public function isConsultor(): bool
    {
        return $this->role === 'consultor';
    }

    public function isCredor(): bool
    {
        return $this->role === 'credor';
    }

    public function canViewAllDividas(): bool
    {
        return $this->isAdmin() || $this->isGestor();
    }

    public function canNegotiate(): bool
    {
        return $this->isAdmin() || $this->isGestor() || $this->isConsultor();
    }
}
