<?php

namespace App\Services\Contractual;

use App\Models\DocumentoSnapshotRevisionContractual;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;

class DocumentTraceabilityService
{
    public function registerSnapshotDocuments(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): void
    {
        $revision->load(['documentos' => function ($q) {
            $q->where(function ($sub) {
                $sub->whereNotNull('texto_extraido')
                    ->orWhereNotNull('texto_ocr');
            })->orderByDesc('id')->limit(6);
        }]);

        foreach ($revision->documentos as $documento) {
            DocumentoSnapshotRevisionContractual::updateOrCreate(
                [
                    'snapshot_revision_contractual_id' => $snapshot->id,
                    'documento_revision_contractual_id' => $documento->id,
                ],
                [
                    'fuente_texto_usada' => $documento->fuente_texto,
                    'estado_extraccion' => $documento->extraccion_estado,
                    'user_id' => auth()->id(),
                ]
            );
        }
    }
}
