Trazabilidad documental entre documentos y snapshots

Incluye:
- migración pivote documento_snapshot_revision_contractual
- modelo DocumentoSnapshotRevisionContractual
- servicio DocumentTraceabilityService
- AnalisisRevisionContractualController actualizado para registrar trazabilidad
- vista documento/show con historial de snapshots usados
- partial para mostrar documentos usados dentro del snapshot

Ajustes manuales adicionales recomendados:

1. En DocumentoRevisionContractual model agregar:
   public function snapshotsTraza()
   {
       return $this->hasMany(DocumentoSnapshotRevisionContractual::class, 'documento_revision_contractual_id')
           ->with(['snapshot', 'usuario'])
           ->latest();
   }

2. En SnapshotRevisionContractual model agregar:
   public function documentosTraza()
   {
       return $this->hasMany(DocumentoSnapshotRevisionContractual::class, 'snapshot_revision_contractual_id')
           ->with(['documento', 'usuario'])
           ->latest();
   }

3. En DocumentoRevisionContractualController@show cargar:
   $documento->load(['snapshotsTraza.snapshot', 'snapshotsTraza.usuario']);

4. En SnapshotRevisionContractualController@show cargar:
   $snapshot->load(['documentosTraza.documento', 'documentosTraza.usuario']);

5. En la vista del snapshot incluir:
   @include('contractual.snapshots.partials.trazabilidad_documentos')

Pasos:
- copiar archivos
- php artisan migrate
- composer dump-autoload
- php artisan optimize:clear

Próximo respaldo Git recomendado:
- después de validar que un snapshot registra correctamente sus documentos usados
