<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MensagemWhatsapp extends Model
{
    use HasFactory;

    protected $table = 'core_mensagemwhatsapp';

    protected $fillable = [
        'mensagem',
        'categoria',
    ];
}
