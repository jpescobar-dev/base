<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContradiccionRevisionContractual extends Model
{
    protected $table = 'contradicciones_revision_contractual';

    protected $fillable = [
        'snapshot_revision_contractual_id',
        'campo',
        'etiqueta',
        'criticidad',
        'descripcion',
        'valores_detectados',
        'recomendacion',
        'documento_preferente',
        'user_id',
    ];

    protected $casts = [
        'valores_detectados' => 'array',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(SnapshotRevisionContractual::class, 'snapshot_revision_contractual_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
