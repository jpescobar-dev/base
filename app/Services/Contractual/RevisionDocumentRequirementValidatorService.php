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
            ->where('obligatorio', true);

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
            $errores[] = 'No existen tipos documentales obligatorios y aplicables configurados para esta revisión.';
        }

        if ($presentesCriticos < 2) {
            $errores[] = 'No existe un mínimo suficiente de documentos críticos presentes para un análisis confiable.';
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
        ];
    }
}
