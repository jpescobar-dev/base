<?php

namespace App\Services\Contractual;

use App\Models\SnapshotRevisionContractual;

class SnapshotEvolutionService
{
    public function compare(SnapshotRevisionContractual $actual, ?SnapshotRevisionContractual $anterior = null): array
    {
        $actual->loadMissing(['hallazgos', 'contradicciones']);
        if ($anterior) {
            $anterior->loadMissing(['hallazgos', 'contradicciones']);
        }

        return [
            'hallazgos' => $this->compararHallazgos($actual, $anterior),
            'contradicciones' => $this->compararContradicciones($actual, $anterior),
        ];
    }

    protected function compararHallazgos(SnapshotRevisionContractual $actual, ?SnapshotRevisionContractual $anterior = null): array
    {
        $curr = collect($actual->hallazgos ?? [])->keyBy(fn ($h) => mb_strtolower(trim(($h->titulo ?? '') . '|' . ($h->tipo_riesgo ?? ''))));
        $prev = $anterior ? collect($anterior->hallazgos ?? [])->keyBy(fn ($h) => mb_strtolower(trim(($h->titulo ?? '') . '|' . ($h->tipo_riesgo ?? '')))) : collect();

        $resultado = [
            'nuevos' => [],
            'persistentes' => [],
            'corregidos' => [],
            'agravados' => [],
        ];

        foreach ($curr as $k => $item) {
            if (!$prev->has($k)) {
                $resultado['nuevos'][] = $item;
                continue;
            }

            $anteriorItem = $prev->get($k);
            $nivelActual = strtolower((string) ($item->nivel_criticidad ?? ''));
            $nivelAnterior = strtolower((string) ($anteriorItem->nivel_criticidad ?? ''));

            if ($this->pesoCriticidad($nivelActual) > $this->pesoCriticidad($nivelAnterior)) {
                $resultado['agravados'][] = [
                    'antes' => $anteriorItem,
                    'ahora' => $item,
                ];
            } else {
                $resultado['persistentes'][] = $item;
            }
        }

        foreach ($prev as $k => $item) {
            if (!$curr->has($k)) {
                $resultado['corregidos'][] = $item;
            }
        }

        return $resultado;
    }

    protected function compararContradicciones(SnapshotRevisionContractual $actual, ?SnapshotRevisionContractual $anterior = null): array
    {
        $curr = collect($actual->contradicciones ?? [])->keyBy(fn ($c) => mb_strtolower(trim((string) ($c->campo ?? $c->etiqueta ?? ''))));
        $prev = $anterior ? collect($anterior->contradicciones ?? [])->keyBy(fn ($c) => mb_strtolower(trim((string) ($c->campo ?? $c->etiqueta ?? '')))) : collect();

        $resultado = [
            'nuevas' => [],
            'persistentes' => [],
            'resueltas' => [],
            'agravadas' => [],
        ];

        foreach ($curr as $k => $item) {
            if (!$prev->has($k)) {
                $resultado['nuevas'][] = $item;
                continue;
            }

            $anteriorItem = $prev->get($k);
            $nivelActual = strtolower((string) ($item->criticidad ?? ''));
            $nivelAnterior = strtolower((string) ($anteriorItem->criticidad ?? ''));

            if ($this->pesoCriticidad($nivelActual) > $this->pesoCriticidad($nivelAnterior)) {
                $resultado['agravadas'][] = [
                    'antes' => $anteriorItem,
                    'ahora' => $item,
                ];
            } else {
                $resultado['persistentes'][] = $item;
            }
        }

        foreach ($prev as $k => $item) {
            if (!$curr->has($k)) {
                $resultado['resueltas'][] = $item;
            }
        }

        return $resultado;
    }

    protected function pesoCriticidad(string $nivel): int
    {
        return match ($nivel) {
            'alta' => 3,
            'media' => 2,
            'baja' => 1,
            default => 0,
        };
    }
}
