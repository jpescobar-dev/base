<?php

namespace App\Services\Contractual;

use App\Models\SnapshotRevisionContractual;

class ContradiccionComparatorService
{
    public function compare(
        SnapshotRevisionContractual $snapshotActual,
        SnapshotRevisionContractual $snapshotAnterior
    ): array {
        $snapshotActual->loadMissing('contradicciones');
        $snapshotAnterior->loadMissing('contradicciones');

        $actual = $snapshotActual->contradicciones->keyBy(fn ($item) => (string) $item->campo . '|' . (string) $item->etiqueta);
        $anterior = $snapshotAnterior->contradicciones->keyBy(fn ($item) => (string) $item->campo . '|' . (string) $item->etiqueta);

        $resultado = [
            'nuevas' => [],
            'resueltas' => [],
            'persistentes' => [],
            'modificadas' => [],
            'resumen' => [
                'nuevas' => 0,
                'resueltas' => 0,
                'persistentes' => 0,
                'modificadas' => 0,
            ],
        ];

        foreach ($actual as $clave => $cActual) {
            if (!$anterior->has($clave)) {
                $resultado['nuevas'][] = $cActual;
                continue;
            }

            $cAnterior = $anterior->get($clave);

            if ($this->sameValues($cActual->valores_detectados, $cAnterior->valores_detectados)) {
                $resultado['persistentes'][] = $cActual;
            } else {
                $resultado['modificadas'][] = [
                    'antes' => $cAnterior,
                    'ahora' => $cActual,
                ];
            }
        }

        foreach ($anterior as $clave => $cAnterior) {
            if (!$actual->has($clave)) {
                $resultado['resueltas'][] = $cAnterior;
            }
        }

        $resultado['resumen']['nuevas'] = count($resultado['nuevas']);
        $resultado['resumen']['resueltas'] = count($resultado['resueltas']);
        $resultado['resumen']['persistentes'] = count($resultado['persistentes']);
        $resultado['resumen']['modificadas'] = count($resultado['modificadas']);

        return $resultado;
    }

    protected function sameValues($a, $b): bool
    {
        return json_encode($this->normalizeArray($a)) === json_encode($this->normalizeArray($b));
    }

    protected function normalizeArray($value): array
    {
        $value = is_array($value) ? $value : [];
        foreach ($value as &$item) {
            if (is_array($item)) {
                ksort($item);
            }
        }
        usort($value, fn ($x, $y) => strcmp(json_encode($x), json_encode($y)));
        return $value;
    }
}
