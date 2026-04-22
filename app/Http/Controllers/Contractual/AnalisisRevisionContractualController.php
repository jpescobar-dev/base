<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\ChecklistRevisionContractual;
use App\Models\ContradiccionRevisionContractual;
use App\Models\DocumentoSnapshotRevisionContractual;
use App\Models\HallazgoRevisionContractual;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use App\Services\Contractual\DocumentContradictionDetectorService;
use App\Services\Contractual\DocumentTraceabilityService;
use App\Services\Contractual\OpenAIRevisionContractualService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AnalisisRevisionContractualController extends Controller
{
    public function store(
        RevisionContractual $revision,
        OpenAIRevisionContractualService $service,
        DocumentContradictionDetectorService $contradictionDetector,
        DocumentTraceabilityService $traceabilityService
    ): RedirectResponse {
        \Log::info('Entró al análisis', [
            'revision_id' => $revision->id,
            'user_id' => auth()->id(),
        ]);

        $data = $service->analizar($revision);

        \Log::info('Respuesta análisis', [
            'revision_id' => $revision->id,
            'keys' => array_keys($data),
        ]);

        $outputText = data_get($data, 'output.0.content.0.text');
        $decoded = null;

        if ($outputText) {
            $decoded = json_decode($outputText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $clean = trim($outputText);

                if (str_starts_with($clean, '"') && str_ends_with($clean, '"')) {
                    $clean = substr($clean, 1, -1);
                }

                $clean = stripslashes($clean);
                $decoded = json_decode($clean, true);
            }
        }

        if (!$decoded || !is_array($decoded)) {
            \Log::warning('No se pudo parsear JSON de OpenAI', [
                'revision_id' => $revision->id,
                'outputText' => $outputText,
            ]);

            return back()->with('error', 'La respuesta de IA no pudo convertirse a JSON estructurado.');
        }

        $jsonFinal = $decoded;
        $contradicciones = $contradictionDetector->detect($revision);
        $jsonFinal['contradicciones_documentales'] = $contradicciones['contradicciones'] ?? [];

        $ultimaVersion = $revision->snapshots()->max('numero_version') ?? 0;
        $nuevaVersion = $ultimaVersion + 1;

        DB::beginTransaction();

        try {
            SnapshotRevisionContractual::where('revision_contractual_id', $revision->id)
                ->update(['es_actual' => false]);

            $snapshot = SnapshotRevisionContractual::create([
                'revision_contractual_id' => $revision->id,
                'numero_version' => $nuevaVersion,
                'tipo_ejecucion' => 'openai',
                'resumen' => $this->buildResumenTexto($jsonFinal),
                'json_resultado' => $jsonFinal,
                'es_actual' => true,
                'user_id' => auth()->id(),
            ]);

            $traceabilityService->registerSnapshotDocuments($revision, $snapshot);

            foreach (($jsonFinal['hallazgos'] ?? []) as $hallazgo) {
                $titulo = $hallazgo['titulo']
                    ?? $hallazgo['clausula']
                    ?? $hallazgo['categoria']
                    ?? 'Hallazgo sin título';

                $tipoRiesgo = $hallazgo['tipo_riesgo']
                    ?? $hallazgo['categoria']
                    ?? null;

                $criticidad = $hallazgo['criticidad']
                    ?? $hallazgo['riesgo']
                    ?? null;

                $descripcion = $hallazgo['descripcion']
                    ?? $hallazgo['observacion']
                    ?? null;

                $recomendacion = $hallazgo['recomendacion']
                    ?? null;

                HallazgoRevisionContractual::create([
                    'snapshot_revision_contractual_id' => $snapshot->id,
                    'estado_id' => null,
                    'titulo' => $titulo,
                    'tipo_hallazgo' => $this->mapTipoHallazgo($criticidad),
                    'tipo_riesgo' => $tipoRiesgo,
                    'nivel_criticidad' => $criticidad,
                    'hecho_acreditado' => null,
                    'observacion' => $descripcion,
                    'fundamento_documental' => null,
                    'consecuencia_posible' => null,
                    'recomendacion' => $recomendacion,
                    'user_id' => auth()->id(),
                ]);
            }

            foreach (($jsonFinal['contradicciones_documentales'] ?? []) as $item) {
                ContradiccionRevisionContractual::create([
                    'snapshot_revision_contractual_id' => $snapshot->id,
                    'campo' => $item['campo'] ?? null,
                    'etiqueta' => $item['etiqueta'] ?? 'Campo sin etiqueta',
                    'criticidad' => $item['criticidad'] ?? 'media',
                    'descripcion' => $item['descripcion'] ?? null,
                    'valores_detectados' => $item['valores'] ?? [],
                    'recomendacion' => $item['recomendacion'] ?? null,
                    'documento_preferente' => $this->resolverDocumentoPreferente($item['valores'] ?? []),
                    'user_id' => auth()->id(),
                ]);
            }

            $orden = 1;

            if (isset($jsonFinal['checklist']['existencia']) || isset($jsonFinal['checklist']['coherencia']) || isset($jsonFinal['checklist']['cumplimiento'])) {
                foreach (['existencia', 'coherencia', 'cumplimiento'] as $capa) {
                    foreach (($jsonFinal['checklist'][$capa] ?? []) as $item) {
                        ChecklistRevisionContractual::create([
                            'snapshot_revision_contractual_id' => $snapshot->id,
                            'item' => $item['item'] ?? 'Ítem sin nombre',
                            'estado_item' => $this->mapEstadoChecklist($item['estado'] ?? null),
                            'tipo_checklist' => $capa,
                            'observacion' => $item['observacion'] ?? null,
                            'referencia_documental' => null,
                            'orden' => $orden++,
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            } else {
                foreach (($jsonFinal['checklist'] ?? []) as $item) {
                    ChecklistRevisionContractual::create([
                        'snapshot_revision_contractual_id' => $snapshot->id,
                        'item' => $item['item'] ?? 'Ítem sin nombre',
                        'estado_item' => $this->mapEstadoChecklist($item['estado'] ?? null),
                        'tipo_checklist' => null,
                        'observacion' => $item['observacion'] ?? null,
                        'referencia_documental' => null,
                        'orden' => $orden++,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            \Log::info('Snapshot IA creado con trazabilidad documental', [
                'snapshot_id' => $snapshot->id,
                'revision_id' => $revision->id,
                'numero_version' => $snapshot->numero_version,
                'hallazgos_count' => count($jsonFinal['hallazgos'] ?? []),
                'contradicciones_count' => count($jsonFinal['contradicciones_documentales'] ?? []),
            ]);

            return redirect()
                ->route('contractual.revisiones.snapshots.show', [$revision, $snapshot])
                ->with('success', 'Análisis ejecutado y registros generados correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error al guardar análisis IA', [
                'revision_id' => $revision->id,
                'mensaje' => $e->getMessage(),
            ]);

            return back()->with('error', 'Ocurrió un error al guardar el análisis IA.');
        }
    }

    protected function resolverDocumentoPreferente(array $valores): ?string
    {
        $jerarquia = [
            'BASES_ADMINISTRATIVAS_GENERALES' => 1,
            'BASES_ADMINISTRATIVAS_ESPECIALES' => 2,
            'BASES_TECNICAS' => 3,
            'RESOLUCION_ADJUDICACION' => 4,
            'CONTRATO' => 5,
            'OFERTA_TECNICA' => 6,
            'OFERTA_ECONOMICA' => 7,
            'GARANTIA' => 8,
            'OTRO' => 9,
        ];

        if (empty($valores)) {
            return null;
        }

        usort($valores, function ($a, $b) use ($jerarquia) {
            $ja = $jerarquia[$a['tipo_documento'] ?? 'OTRO'] ?? 99;
            $jb = $jerarquia[$b['tipo_documento'] ?? 'OTRO'] ?? 99;
            return $ja <=> $jb;
        });

        return $valores[0]['documento'] ?? null;
    }

    protected function buildResumenTexto(array $jsonFinal): string
    {
        $resumen = $jsonFinal['resumen'] ?? [];
        $partes = [];

        if (!empty($resumen['tipo_contrato'])) {
            $partes[] = 'Tipo: ' . $resumen['tipo_contrato'];
        }

        if (!empty($resumen['entidad'])) {
            $partes[] = 'Entidad: ' . $resumen['entidad'];
        }

        if (!empty($resumen['riesgo_general'])) {
            $partes[] = 'Riesgo general: ' . $resumen['riesgo_general'];
        }

        if (!empty($resumen['observaciones_clave']) && is_array($resumen['observaciones_clave'])) {
            $partes[] = 'Observaciones clave: ' . implode(' | ', $resumen['observaciones_clave']);
        }

        return implode(' || ', $partes);
    }

    protected function mapTipoHallazgo(?string $criticidad): ?string
    {
        return match (strtolower((string) $criticidad)) {
            'alta' => 'critico',
            'media' => 'relevante',
            'baja' => 'observacion_menor',
            default => null,
        };
    }

    protected function mapEstadoChecklist(?string $estado): string
    {
        return match (strtolower((string) $estado)) {
            'cumple' => 'cumple',
            'no_cumple' => 'no_cumple',
            'no_se_encuentra' => 'no_se_encuentra',
            'pendiente' => 'pendiente_verificar',
            default => 'pendiente_verificar',
        };
    }
}
