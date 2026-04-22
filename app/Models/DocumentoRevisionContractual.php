<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoRevisionContractual extends Model
{
    protected $table = 'documentos_revision_contractual';

    protected $fillable = [
        'revision_contractual_id',
        'nombre_original',
        'ruta',
        'mime_type',
        'extension',
        'peso_bytes',
        'hash_archivo',
        'tipo_documento',
        'texto_extraido',
        'texto_ocr',
        'extraccion_estado',
        'ocr_estado',
        'tiene_texto_extraible',
        'fuente_texto',
        'user_id',
    ];

    protected $casts = [
        'tiene_texto_extraible' => 'boolean',
    ];

    public function revision(): BelongsTo
    {
        return $this->belongsTo(RevisionContractual::class, 'revision_contractual_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
