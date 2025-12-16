<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocTitulo extends Model
{
    use HasFactory;

    protected $table = 'tipo_doc_titulo';

    protected $fillable = [
        'name',
    ];

    // Relacionamentos
    public function titulos(): HasMany
    {
        return $this->hasMany(Titulo::class, 'tipo_doc_id');
    }
}
