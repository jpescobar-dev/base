<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use Illuminate\Contracts\View\View;

class ComparadorSnapshotController extends Controller
{
    public function compare(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot1,
        SnapshotRevisionContractual $snapshot2
    ): View {
        $snapshot1->load(['hallazgos', 'checklist']);
        $snapshot2->load(['hallazgos', 'checklist']);

        if (
            (int) $snapshot1->revision_contractual_id !== (int) $revision->id ||
            (int) $snapshot2->revision_contractual_id !== (int) $revision->id
        ) {
            abort(404);
        }

        $hallazgos1 = $snapshot1->hallazgos->keyBy(fn ($h) => mb_strtolower(trim((string) $h->titulo)));
        $hallazgos2 = $snapshot2->hallazgos->keyBy(fn ($h) => mb_strtolower(trim((string) $h->titulo)));

        $hallazgosNuevos = $hallazgos2->filter(fn ($h, $k) => !$hallazgos1->has($k))->values();
        $hallazgosEliminados = $hallazgos1->filter(fn ($h, $k) => !$hallazgos2->has($k))->values();

        $hallazgosModificados = collect();

        foreach ($hallazgos2 as $key => $nuevo) {
            if (!$hallazgos1->has($key)) {
                continue;
            }

            $anterior = $hallazgos1->get($key);

            $cambios = [];

            if (($anterior->nivel_criticidad ?? null) !== ($nuevo->nivel_criticidad ?? null)) {
                $cambios[] = [
                    'campo' => 'nivel_criticidad',
                    'antes' => $anterior->nivel_criticidad,
                    'despues' => $nuevo->nivel_criticidad,
                ];
            }

            if (($anterior->tipo_riesgo ?? null) !== ($nuevo->tipo_riesgo ?? null)) {
                $cambios[] = [
                    'campo' => 'tipo_riesgo',
                    'antes' => $anterior->tipo_riesgo,
                    'despues' => $nuevo->tipo_riesgo,
                ];
            }

            if (($anterior->observacion ?? null) !== ($nuevo->observacion ?? null)) {
                $cambios[] = [
                    'campo' => 'observacion',
                    'antes' => $anterior->observacion,
                    'despues' => $nuevo->observacion,
                ];
            }

            if (($anterior->recomendacion ?? null) !== ($nuevo->recomendacion ?? null)) {
                $cambios[] = [
                    'campo' => 'recomendacion',
                    'antes' => $anterior->recomendacion,
                    'despues' => $nuevo->recomendacion,
                ];
            }

            if (!empty($cambios)) {
                $hallazgosModificados->push([
                    'titulo' => $nuevo->titulo,
                    'antes' => $anterior,
                    'despues' => $nuevo,
                    'cambios' => $cambios,
                ]);
            }
        }

        $checklist1 = $snapshot1->checklist->keyBy(fn ($c) => mb_strtolower(trim((string) $c->tipo_checklist . '|' . $c->item)));
        $checklist2 = $snapshot2->checklist->keyBy(fn ($c) => mb_strtolower(trim((string) $c->tipo_checklist . '|' . $c->item)));

        $checklistNuevos = $checklist2->filter(fn ($c, $k) => !$checklist1->has($k))->values();
        $checklistEliminados = $checklist1->filter(fn ($c, $k) => !$checklist2->has($k))->values();

        $checklistCambios = collect();
        $mejorados = 0;
        $empeorados = 0;

        foreach ($checklist2 as $key => $nuevo) {
            if (!$checklist1->has($key)) {
                continue;
            }

            $anterior = $checklist1->get($key);

            $estadoAntes = (string) $anterior->estado_item;
            $estadoDespues = (string) $nuevo->estado_item;

            if ($estadoAntes !== $estadoDespues || (string) $anterior->observacion !== (string) $nuevo->observacion) {
                $clasificacion = $this->clasificarCambioEstado($estadoAntes, $estadoDespues);

                if ($clasificacion === 'mejorado') {
                    $mejorados++;
                }

                if ($clasificacion === 'empeorado') {
                    $empeorados++;
                }

                $checklistCambios->push([
                    'tipo_checklist' => $nuevo->tipo_checklist,
                    'item' => $nuevo->item,
                    'estado_antes' => $estadoAntes,
                    'estado_despues' => $estadoDespues,
                    'observacion_antes' => $anterior->observacion,
                    'observacion_despues' => $nuevo->observacion,
                    'clasificacion' => $clasificacion,
                ]);
            }
        }

        $resumen = [
            'hallazgos_nuevos' => $hallazgosNuevos->count(),
            'hallazgos_eliminados' => $hallazgosEliminados->count(),
            'hallazgos_modificados' => $hallazgosModificados->count(),
            'checklist_nuevos' => $checklistNuevos->count(),
            'checklist_eliminados' => $checklistEliminados->count(),
            'checklist_mejorados' => $mejorados,
            'checklist_empeorados' => $empeorados,
        ];

        $snapshots = $revision->snapshots()->orderBy('numero_version', 'desc')->get();

        return view('contractual.snapshots.compare', compact(
            'revision',
            'snapshot1',
            'snapshot2',
            'snapshots',
            'hallazgosNuevos',
            'hallazgosEliminados',
            'hallazgosModificados',
            'checklistNuevos',
            'checklistEliminados',
            'checklistCambios',
            'resumen'
        ));
    }

    protected function clasificarCambioEstado(string $antes, string $despues): string
    {
        $rank = [
            'cumple' => 4,
            'pendiente_verificar' => 3,
            'no_se_encuentra' => 2,
            'no_cumple' => 1,
        ];

        $rAntes = $rank[$antes] ?? 0;
        $rDespues = $rank[$despues] ?? 0;

        if ($rDespues > $rAntes) {
            return 'mejorado';
        }

        if ($rDespues < $rAntes) {
            return 'empeorado';
        }

        return 'cambio';
    }
}
