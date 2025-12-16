<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailEnvio extends Model
{
    use HasFactory;

    protected $table = 'emails_envio';

    protected $fillable = [
        'email',
        'autenticacao',
        'porta',
        'servidor_smtp',
        'tipo_envio',
        'provedor',
        'senha',
    ];

    protected $casts = [
        'porta' => 'integer',
    ];

    protected $hidden = [
        'senha',
    ];
}
