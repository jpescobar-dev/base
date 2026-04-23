<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumentoContractual extends Model
{
    protected $table = 'tipos_documento_contractual';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'jerarquia',
        'peso_jerarquico',
        'es_critico',
        'activo',
    ];

    protected $casts = [
        'es_critico' => 'boolean',
        'activo' => 'boolean',
    ];

    public function revisiones(): HasMany
    {
        return $this->hasMany(RevisionTipoDocumento::class, 'tipo_documento_contractual_id');
    }
}
