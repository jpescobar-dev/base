<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoSnapshotRevisionContractual extends Model
{
    protected $table = 'documento_snapshot_revision_contractual';

    protected $fillable = [
        'snapshot_revision_contractual_id',
        'documento_revision_contractual_id',
        'fuente_texto_usada',
        'estado_extraccion',
        'user_id',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(SnapshotRevisionContractual::class, 'snapshot_revision_contractual_id');
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(DocumentoRevisionContractual::class, 'documento_revision_contractual_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
