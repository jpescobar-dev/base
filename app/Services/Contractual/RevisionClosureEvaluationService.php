<?php

namespace App\Services\Contractual;

use App\Models\SnapshotRevisionContractual;

class RevisionClosureEvaluationService
{
    public function evaluate(SnapshotRevisionContractual $snapshot, array $gate = [], array $evolucion = []): array
    {
        $snapshot->loadMissing(['hallazgos', 'contradicciones']);

        $motivos = [];
        $estado = 'apta';

        $hallazgosAltos = collect($snapshot->hallazgos ?? [])
            ->filter(fn ($h) => strtolower((string) ($h->nivel_criticidad ?? '')) === 'alta')
            ->count();

        $contradiccionesAltas = collect($snapshot->contradicciones ?? [])
            ->filter(fn ($c) => strtolower((string) ($c->criticidad ?? '')) === 'alta')
            ->count();

        $agravadosHallazgos = count(data_get($evolucion, 'hallazgos.agravados', []));
        $agravadasContradicciones = count(data_get($evolucion, 'contradicciones.agravadas', []));

        if (($gate['estado'] ?? 'no_apto') !== 'apto') {
            $estado = 'no_apta';
            $motivos[] = 'La revisión no cumple con el gate documental mínimo.';
        }

        if ($hallazgosAltos > 0) {
            $estado = 'no_apta';
            $motivos[] = "Existen {$hallazgosAltos} hallazgo(s) de criticidad alta.";
        }

        if ($contradiccionesAltas > 0) {
            $estado = 'no_apta';
            $motivos[] = "Existen {$contradiccionesAltas} contradicción(es) de criticidad alta.";
        }

        if ($agravadosHallazgos > 0 || $agravadasContradicciones > 0) {
            $estado = 'no_apta';
            $motivos[] = 'La evolución presenta elementos agravados respecto del snapshot anterior.';
        }

        if (
            $estado === 'apta' &&
            (
                collect($snapshot->hallazgos ?? [])->count() > 0 ||
                collect($snapshot->contradicciones ?? [])->count() > 0
            )
        ) {
            $estado = 'observada';
            $motivos[] = 'La revisión no presenta criticidad alta, pero mantiene observaciones activas.';
        }

        $resumen = match ($estado) {
            'apta' => 'La revisión se encuentra apta para cierre preliminar.',
            'observada' => 'La revisión puede continuar, pero mantiene observaciones que deben ser monitoreadas.',
            default => 'La revisión no se encuentra en condiciones de cierre.',
        };

        return [
            'estado' => $estado,
            'motivos' => $motivos,
            'resumen' => $resumen,
            'metricas' => [
                'hallazgos_altos' => $hallazgosAltos,
                'contradicciones_altas' => $contradiccionesAltas,
                'agravados_hallazgos' => $agravadosHallazgos,
                'agravadas_contradicciones' => $agravadasContradicciones,
            ],
        ];
    }
}
