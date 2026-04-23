<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;

class RevisionReportService
{
    public function build(RevisionContractual $revision, SnapshotRevisionContractual $snapshot, array $gate = []): array
    {
        $snapshot->load([
            'hallazgos',
            'checklist',
            'contradicciones',
            'documentosTraza.documento',
            'documentosTraza.usuario',
        ]);

        $json = $snapshot->json_resultado ?? [];
        $resumen = $json['resumen'] ?? [];
        $contradiccionesRaw = $json['contradicciones_documentales'] ?? [];

        return [
            'revision' => [
                'id' => $revision->id,
                'titulo' => $revision->titulo,
                'descripcion' => $revision->descripcion,
            ],
            'snapshot' => [
                'id' => $snapshot->id,
                'version' => $snapshot->numero_version,
                'tipo_ejecucion' => $snapshot->tipo_ejecucion,
                'fecha' => optional($snapshot->created_at)->format('d-m-Y H:i'),
            ],
            'gate' => $gate,
            'resumen' => [
                'tipo_contrato' => $resumen['tipo_contrato'] ?? null,
                'entidad' => $resumen['entidad'] ?? null,
                'riesgo_general' => $resumen['riesgo_general'] ?? null,
                'observaciones_clave' => $resumen['observaciones_clave'] ?? [],
            ],
            'hallazgos' => $snapshot->hallazgos->map(function ($h) {
                return [
                    'titulo' => $h->titulo,
                    'criticidad' => $h->nivel_criticidad,
                    'tipo_riesgo' => $h->tipo_riesgo,
                    'observacion' => $h->observacion,
                    'recomendacion' => $h->recomendacion,
                ];
            })->values()->all(),
            'checklist' => [
                'existencia' => $snapshot->checklist->where('tipo_checklist', 'existencia')->values()->map(fn ($i) => $this->mapChecklist($i))->all(),
                'coherencia' => $snapshot->checklist->where('tipo_checklist', 'coherencia')->values()->map(fn ($i) => $this->mapChecklist($i))->all(),
                'cumplimiento' => $snapshot->checklist->where('tipo_checklist', 'cumplimiento')->values()->map(fn ($i) => $this->mapChecklist($i))->all(),
            ],
            'contradicciones' => collect($contradiccionesRaw)->map(function ($c) {
                return [
                    'campo' => $c['campo'] ?? null,
                    'etiqueta' => $c['etiqueta'] ?? null,
                    'criticidad' => $c['criticidad'] ?? null,
                    'descripcion' => $c['descripcion'] ?? null,
                    'valores' => $c['valores'] ?? [],
                    'documento_prevalente' => $c['documento_prevalente'] ?? null,
                    'tipo_documento_prevalente' => $c['tipo_documento_prevalente'] ?? null,
                    'peso_prevalente' => $c['peso_prevalente'] ?? null,
                    'valor_prevalente' => $c['valor_prevalente'] ?? null,
                    'motivo_prevalencia' => $c['motivo_prevalencia'] ?? null,
                    'recomendacion' => $c['recomendacion'] ?? null,
                ];
            })->values()->all(),
            'documentos_utilizados' => $snapshot->documentosTraza->map(function ($t) {
                return [
                    'documento' => $t->documento->nombre_original ?? 'Documento',
                    'fuente_texto_usada' => $t->fuente_texto_usada,
                    'estado_extraccion' => $t->estado_extraccion,
                    'usuario' => $t->usuario->name ?? 'N/D',
                    'fecha' => optional($t->created_at)->format('d-m-Y H:i'),
                ];
            })->values()->all(),
        ];
    }

    protected function mapChecklist($item): array
    {
        return [
            'item' => $item->item,
            'estado' => $item->estado_item,
            'observacion' => $item->observacion,
        ];
    }
}
