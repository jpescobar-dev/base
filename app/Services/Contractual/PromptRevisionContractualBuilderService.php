<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use Illuminate\Support\Facades\Storage;

class PromptRevisionContractualBuilderService
{
    public function build(RevisionContractual $revision): string
    {
        $capas = [
            '01_contexto.md',
            '02_objetivo.md',
            '03_datos.md',
            '04_restricciones.md',
            '05_estilo_tono.md',
            '06_formato_salida.md',
        ];

        $bloques = [];

        foreach ($capas as $archivo) {
            $ruta = 'prompts/contractual/' . $archivo;
            if (Storage::disk('local')->exists($ruta)) {
                $bloques[] = Storage::disk('local')->get($ruta);
            }
        }

        $revision->load(['documentos.tipoDocumento', 'tiposDocumentoConfigurados.tipo']);

        $documentos = $revision->documentos
            ->filter(fn ($d) => !empty($d->texto_extraido) || !empty($d->texto_ocr))
            ->map(function ($d) {
                $texto = $d->texto_extraido ?: $d->texto_ocr;
                return [
                    'nombre' => $d->nombre_original,
                    'tipo' => $d->tipoDocumento->nombre ?? $d->tipo_documento ?? 'Sin clasificar',
                    'fuente' => $d->fuente_texto,
                    'texto' => mb_substr((string) $texto, 0, 12000),
                ];
            })
            ->values()
            ->all();

        $tiposAplicables = $revision->tiposDocumentoConfigurados
            ->where('aplica', true)
            ->map(function ($cfg) {
                return [
                    'tipo' => $cfg->tipo?->nombre,
                    'obligatorio' => $cfg->obligatorio,
                    'observacion' => $cfg->observacion,
                ];
            })
            ->values()
            ->all();

        $contextoDinamico = [
            'revision_id' => $revision->id,
            'titulo' => $revision->titulo,
            'descripcion' => $revision->descripcion,
            'etapa_proceso_contractual' => $revision->etapa_proceso_contractual,
            'tipos_documentales_aplicables' => $tiposAplicables,
            'documentos' => $documentos,
        ];

        $bloques[] = "## CONTEXTO DINÁMICO DE LA REVISIÓN\n" . json_encode($contextoDinamico, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return implode("\n\n", $bloques);
    }
}
