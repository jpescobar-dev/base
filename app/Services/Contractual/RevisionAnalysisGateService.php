<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;

class RevisionAnalysisGateService
{
    public function evaluate(RevisionContractual $revision): array
    {
        $revision->load([
            'tiposDocumentoConfigurados.tipo',
            'documentos.tipoDocumento',
        ]);

        $requeridos = $revision->tiposDocumentoConfigurados
            ->where('aplica', true)
            ->where('obligatorio', true);

        $presentes = [];
        $faltantes = [];
        $criticosPresentes = 0;

        foreach ($requeridos as $cfg) {
            $tipo = $cfg->tipo;
            if (!$tipo) continue;

            $doc = $revision->documentos
                ->where('tipo_documento_contractual_id', $tipo->id)
                ->first();

            if ($doc) {
                $presentes[] = $tipo->nombre;

                if ($tipo->es_critico) {
                    $criticosPresentes++;
                }
            } else {
                $faltantes[] = $tipo->nombre;
            }
        }

        $estado = 'apto';
        $mensajes = [];

        if ($requeridos->count() === 0) {
            $estado = 'no_apto';
            $mensajes[] = 'No hay configuración documental obligatoria definida.';
        }

        if (count($faltantes) > 0) {
            $estado = 'no_apto';
            $mensajes[] = 'Faltan documentos obligatorios.';
        }

        if ($criticosPresentes < 2) {
            $estado = 'no_apto';
            $mensajes[] = 'No hay suficientes documentos críticos.';
        }

        return [
            'estado' => $estado,
            'mensajes' => $mensajes,
            'presentes' => $presentes,
            'faltantes' => $faltantes,
            'criticos_presentes' => $criticosPresentes,
            'total_obligatorios' => $requeridos->count(),
        ];
    }
}
