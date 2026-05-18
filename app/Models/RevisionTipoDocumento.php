<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionTipoDocumento extends Model
{
    protected $table = 'revision_tipos_documento';

    protected $fillable = [
        'revision_contractual_id',
        'tipo_documento_contractual_id',
        'aplica',
        'obligatorio',
        'observacion',
        'user_id',
    ];

    protected $casts = [
        'aplica' => 'boolean',
        'obligatorio' => 'boolean',
    ];

    public function revision(): BelongsTo
    {
        return $this->belongsTo(RevisionContractual::class, 'revision_contractual_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoDocumentoContractual::class, 'tipo_documento_contractual_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
