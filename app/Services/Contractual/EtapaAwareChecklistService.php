<?php

namespace App\Services\Contractual;

class EtapaAwareChecklistService
{
    public function ajustar(array $checklist, string $etapa): array
    {
        return array_map(function ($item) use ($etapa) {

            $nombre = strtolower($item['item'] ?? '');

            if ($this->noAplicaPorEtapa($nombre, $etapa)) {
                $item['estado'] = 'no_aplica';
                $item['observacion'] = 'No aplica en esta etapa del proceso.';
                return $item;
            }

            if ($this->pendientePorEtapa($nombre, $etapa)) {
                $item['estado'] = 'pendiente_etapa';
                $item['observacion'] = 'Pendiente según etapa del proceso.';
                return $item;
            }

            return $item;

        }, $checklist);
    }

    protected function noAplicaPorEtapa(string $item, string $etapa): bool
    {
        if ($etapa === 'pre_adjudicacion') {
            return str_contains($item, 'contrato') || str_contains($item, 'garantía');
        }

        return false;
    }

    protected function pendientePorEtapa(string $item, string $etapa): bool
    {
        if ($etapa === 'adjudicado_sin_contrato') {
            return str_contains($item, 'contrato');
        }

        return false;
    }
}
