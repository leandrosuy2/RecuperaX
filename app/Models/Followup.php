<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    use HasFactory;

    protected $table = 'follow_up';

    public $timestamps = false;

    protected $fillable = [
        'empresa_id',
        'devedor_id',
        'texto',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id')->withDefault();
    }

    public function devedor(): BelongsTo
    {
        return $this->belongsTo(Devedor::class, 'devedor_id')->withDefault();
    }
}
