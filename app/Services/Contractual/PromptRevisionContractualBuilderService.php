<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use Illuminate\Support\Facades\Storage;

class PromptRevisionContractualBuilderService
{
    protected string $basePath = 'prompts/contractual/';

    public function build(RevisionContractual $revision): string
    {
        $partes = [
            $this->read('01_contexto.md'),
            $this->read('02_objetivo.md'),
            $this->read('03_datos.md'),
            $this->read('04_restricciones.md'),
            $this->read('05_estilo.md'),
            $this->read('06_formato_salida.md'),
        ];

        $contenidoBase = implode("\n\n", $partes);
        $datosRevision = $this->buildDatosRevision($revision);

        return $contenidoBase . "\n\n" . $datosRevision;
    }

    protected function read(string $file): string
    {
        $path = $this->basePath . $file;

        if (!Storage::disk('local')->exists($path)) {
            throw new \RuntimeException("No se encontró el archivo de instrucciones: {$path}");
        }

        $contenido = Storage::disk('local')->get($path);

        if (!is_string($contenido) || trim($contenido) === '') {
            throw new \RuntimeException("El archivo de instrucciones está vacío o no es legible: {$path}");
        }

        return $contenido;
    }

    protected function buildDatosRevision(RevisionContractual $revision): string
    {
        $revision->load(['documentos' => function ($q) {
            $q->where(function ($sub) {
                $sub->whereNotNull('texto_extraido')
                    ->orWhereNotNull('texto_ocr');
            })
            ->orderByDesc('id')
            ->limit(6);
        }]);

        $documentos = $revision->documentos->map(function ($doc) {
            $tipo = $this->normalizarTipoDocumento(
                $doc->tipo_documento,
                $doc->nombre_original
            );

            $estadoExtraccion = $doc->extraccion_estado ?: 'PENDIENTE';
            $textoFuente = $doc->texto_extraido ?: $doc->texto_ocr;

            if (!empty($textoFuente)) {
                $texto = trim($textoFuente);
                $texto = mb_substr($texto, 0, 8000);

                return <<<TXT
### DOCUMENTO: {$doc->nombre_original}
TIPO_DOCUMENTO: {$tipo}
ESTADO_EXTRACCION: {$estadoExtraccion}

{$texto}
TXT;
            }

            return <<<TXT
### DOCUMENTO: {$doc->nombre_original}
TIPO_DOCUMENTO: {$tipo}
ESTADO_EXTRACCION: {$estadoExtraccion}

No fue posible extraer texto legible del documento.
TXT;
        })->implode("\n\n");

        if (blank($documentos)) {
            $documentos = '- No existen documentos cargados en la revisión.';
        }

        $descripcion = $revision->descripcion ?: 'Sin descripción';

        $contradicciones = app(DocumentContradictionDetectorService::class)->detect($revision);
        $bloqueContradicciones = $this->buildContradictionBlock($contradicciones);

        return <<<TXT
# DOCUMENTACIÓN A ANALIZAR

## Identificación de la revisión
- ID revisión: {$revision->id}
- Título: {$revision->titulo}
- Descripción: {$descripcion}

## Documentos disponibles
{$documentos}

{$bloqueContradicciones}

## Instrucción final
Realiza la revisión contractual preliminar aplicando estrictamente las reglas anteriores.
Aplica jerarquía documental cuando existan contradicciones.
Trabaja solo con la información disponible.
Si faltan antecedentes, indícalo expresamente.
TXT;
    }

    protected function buildContradictionBlock(array $contradicciones): string
    {
        $items = $contradicciones['contradicciones'] ?? [];

        if (empty($items)) {
            return "## Contradicciones preliminares detectadas\n- No se detectaron contradicciones heurísticas entre documentos.";
        }

        $lineas = ["## Contradicciones preliminares detectadas"];

        foreach ($items as $item) {
            $lineas[] = "- Campo: {$item['etiqueta']} | Criticidad: {$item['criticidad']}";
            $lineas[] = "  Descripción: {$item['descripcion']}";
            foreach ($item['valores'] as $valor) {
                $lineas[] = "  * {$valor['documento']}: {$valor['valor']}";
            }
            $lineas[] = "  Recomendación: {$item['recomendacion']}";
        }

        return implode("\n", $lineas);
    }

    protected function normalizarTipoDocumento(?string $tipoDocumento, ?string $nombreOriginal): string
    {
        $base = mb_strtolower(trim(($tipoDocumento ?: '') . ' ' . ($nombreOriginal ?: '')));

        return match (true) {
            str_contains($base, 'contrato') => 'CONTRATO',
            str_contains($base, 'resolución de adjudicación'),
            str_contains($base, 'resolucion de adjudicacion'),
            str_contains($base, 'adjudicación'),
            str_contains($base, 'adjudicacion') => 'RESOLUCION_ADJUDICACION',
            str_contains($base, 'bases administrativas especiales') => 'BASES_ADMINISTRATIVAS_ESPECIALES',
            str_contains($base, 'bases técnicas'),
            str_contains($base, 'bases tecnicas') => 'BASES_TECNICAS',
            str_contains($base, 'bases administrativas generales'),
            str_contains($base, 'bases administrativas') => 'BASES_ADMINISTRATIVAS_GENERALES',
            str_contains($base, 'oferta técnica'),
            str_contains($base, 'oferta tecnica') => 'OFERTA_TECNICA',
            str_contains($base, 'oferta económica'),
            str_contains($base, 'oferta economica'),
            str_contains($base, 'formulario economico') => 'OFERTA_ECONOMICA',
            str_contains($base, 'garantía'),
            str_contains($base, 'garantia') => 'GARANTIA',
            default => 'OTRO',
        };
    }
}
