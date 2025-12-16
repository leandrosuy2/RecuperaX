<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMensagemWhatsapp extends Model
{
    use HasFactory;

    protected $table = 'template_mensagem_whatsapp';

    protected $fillable = [
        'nome',
        'categoria',
        'template_mensagem',
        'follow_up_automatico',
        'ativo',
    ];

    protected $casts = [
        'follow_up_automatico' => 'boolean',
        'ativo' => 'boolean',
    ];
}
