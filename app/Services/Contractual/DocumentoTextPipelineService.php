<?php

namespace App\Services\Contractual;

class DocumentoTextPipelineService
{
    public function __construct(
        protected PdfTextExtractorService $pdfTextExtractorService,
        protected PdfOcrExtractorService $pdfOcrExtractorService,
        protected WordTextExtractorService $wordTextExtractorService
    ) {
    }

    public function process(string $relativePath, string $extension): array
    {
        $extension = strtolower(trim($extension));

        if ($extension === 'pdf') {
            $extraido = $this->pdfTextExtractorService->extractFromPublicPath($relativePath);

            if (($extraido['estado'] ?? null) === 'EXTRAIDO' && !empty($extraido['texto'])) {
                return [
                    'texto_final' => $extraido['texto'],
                    'texto_extraido' => $extraido['texto'],
                    'texto_ocr' => null,
                    'extraccion_estado' => 'EXTRAIDO',
                    'ocr_estado' => null,
                    'tiene_texto_extraible' => true,
                    'fuente_texto' => 'texto',
                    'mensaje' => $extraido['mensaje'] ?? 'Texto extraído correctamente.',
                ];
            }

            $ocr = $this->pdfOcrExtractorService->extractFromPublicPath($relativePath);

            if (($ocr['estado'] ?? null) === 'OCR_EXTRAIDO' && !empty($ocr['texto'])) {
                return [
                    'texto_final' => $ocr['texto'],
                    'texto_extraido' => null,
                    'texto_ocr' => $ocr['texto'],
                    'extraccion_estado' => 'EXTRAIDO_OCR',
                    'ocr_estado' => 'OCR_EXTRAIDO',
                    'tiene_texto_extraible' => false,
                    'fuente_texto' => 'ocr',
                    'mensaje' => $ocr['mensaje'] ?? 'Texto OCR extraído correctamente.',
                ];
            }

            return [
                'texto_final' => null,
                'texto_extraido' => null,
                'texto_ocr' => null,
                'extraccion_estado' => $extraido['estado'] ?? 'SIN_TEXTO',
                'ocr_estado' => $ocr['estado'] ?? null,
                'tiene_texto_extraible' => false,
                'fuente_texto' => null,
                'mensaje' => $ocr['mensaje'] ?? $extraido['mensaje'] ?? 'No fue posible extraer texto del documento.',
            ];
        }

        if ($extension === 'docx') {
            $word = $this->wordTextExtractorService->extractFromPublicPath($relativePath);

            if (($word['estado'] ?? null) === 'EXTRAIDO' && !empty($word['texto'])) {
                return [
                    'texto_final' => $word['texto'],
                    'texto_extraido' => $word['texto'],
                    'texto_ocr' => null,
                    'extraccion_estado' => 'EXTRAIDO',
                    'ocr_estado' => null,
                    'tiene_texto_extraible' => true,
                    'fuente_texto' => 'word',
                    'mensaje' => $word['mensaje'] ?? 'Texto Word extraído correctamente.',
                ];
            }

            return [
                'texto_final' => null,
                'texto_extraido' => null,
                'texto_ocr' => null,
                'extraccion_estado' => $word['estado'] ?? 'SIN_TEXTO',
                'ocr_estado' => null,
                'tiene_texto_extraible' => false,
                'fuente_texto' => 'word',
                'mensaje' => $word['mensaje'] ?? 'No fue posible extraer texto del archivo Word.',
            ];
        }

        return [
            'texto_final' => null,
            'texto_extraido' => null,
            'texto_ocr' => null,
            'extraccion_estado' => 'FORMATO_NO_SOPORTADO',
            'ocr_estado' => null,
            'tiene_texto_extraible' => false,
            'fuente_texto' => null,
            'mensaje' => 'Formato no soportado. Solo PDF o DOCX.',
        ];
    }
}
