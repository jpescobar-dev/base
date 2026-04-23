<?php

namespace App\Services\Contractual;

class EtapaAwarePromptRulesService
{
    public function reglas(string $etapa): string
    {
        return match ($etapa) {
            'pre_adjudicacion' => $this->preAdjudicacion(),
            'adjudicado_sin_contrato' => $this->adjudicadoSinContrato(),
            'contratado' => $this->contratado(),
            'en_ejecucion' => $this->enEjecucion(),
            default => ''
        };
    }

    protected function preAdjudicacion(): string
    {
        return "No marcar como incumplimiento la ausencia de contrato o garantía. Enfocar análisis en bases, ofertas y evaluación.";
    }

    protected function adjudicadoSinContrato(): string
    {
        return "Considerar contrato como pendiente, no incumplimiento. Validar resolución de adjudicación y CDP.";
    }

    protected function contratado(): string
    {
        return "Validar coherencia entre contrato, bases y oferta. Evaluar garantía de fiel cumplimiento.";
    }

    protected function enEjecucion(): string
    {
        return "Evaluar cumplimiento contractual, vigencia de garantías, plazos y posibles incumplimientos.";
    }
}
