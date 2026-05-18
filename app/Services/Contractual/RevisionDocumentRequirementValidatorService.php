<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;

class RevisionDocumentRequirementValidatorService
{
    public function validate(RevisionContractual $revision): array
    {
        $revision->load([
            'tiposDocumentoConfigurados.tipo',
            'documentos.tipoDocumento',
        ]);

        $requeridos = $revision->tiposDocumentoConfigurados
            ->where('aplica', true)
            ->where('obligatorio', true)
            ->filter(function ($cfg) use ($revision) {
                return $this->aplicaSegunEtapa($revision->etapa_proceso_contractual, $cfg->tipo?->codigo);
            });

        $faltantes = [];
        $presentesCriticos = 0;

        foreach ($requeridos as $cfg) {
            $tipo = $cfg->tipo;
            if (!$tipo) {
                continue;
            }

            $encontrado = $revision->documentos
                ->where('tipo_documento_contractual_id', $tipo->id)
                ->first();

            if (!$encontrado) {
                $faltantes[] = $tipo->nombre;
            } elseif ($tipo->es_critico) {
                $presentesCriticos++;
            }
        }

        $errores = [];

        if ($requeridos->count() === 0) {
            $errores[] = 'No existen tipos documentales obligatorios y aplicables para la etapa seleccionada.';
        }

        if ($presentesCriticos < 2) {
            $errores[] = 'No existe un mínimo suficiente de documentos críticos presentes para un análisis confiable en esta etapa.';
        }

        if (!empty($faltantes)) {
            $errores[] = 'Faltan documentos obligatorios: ' . implode(', ', $faltantes) . '.';
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'faltantes' => $faltantes,
            'obligatorios_configurados' => $requeridos->count(),
            'criticos_presentes' => $presentesCriticos,
            'etapa' => $revision->etapa_proceso_contractual,
        ];
    }

    protected function aplicaSegunEtapa(?string $etapa, ?string $codigo): bool
    {
        $etapa = $etapa ?: 'pre_adjudicacion';
        $codigo = $codigo ?: '';

        $bloqueados = [
            'pre_adjudicacion' => [
                'CONTRATO_SUMINISTRO_SERVICIO',
                'GARANTIA_FIEL_CUMPLIMIENTO',
                'ORDEN_COMPRA',
            ],
            'adjudicado_sin_contrato' => [
                'GARANTIA_FIEL_CUMPLIMIENTO',
            ],
            'contratado' => [],
            'en_ejecucion' => [],
        ];

        return !in_array($codigo, $bloqueados[$etapa] ?? [], true);
    }
}
