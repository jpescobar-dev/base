<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tipo_documento_contractual_id',
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

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumentoContractual::class, 'tipo_documento_contractual_id');
    }

    public function snapshotsTraza(): HasMany
    {
        return $this->hasMany(DocumentoSnapshotRevisionContractual::class, 'documento_revision_contractual_id')
            ->with(['snapshot', 'usuario'])
            ->latest();
    }
}
