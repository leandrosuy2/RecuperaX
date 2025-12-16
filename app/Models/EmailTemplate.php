<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'core_emailtemplate';

    protected $fillable = [
        'tipo_envio',
        'mensagem',
    ];
}
