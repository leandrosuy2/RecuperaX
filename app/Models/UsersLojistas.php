<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsersLojistas extends Model
{
    use HasFactory;

    protected $table = 'core_userslojistas';

    protected $fillable = [
        'empresa_id',
        'name',
        'email',
        'google_id',
        'credit',
        'email_credit',
        'whatsapp_credit',
        'address',
        'image',
        'password',
        'status',
    ];

    protected $casts = [
        'credit' => 'integer',
        'email_credit' => 'integer',
        'status' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
