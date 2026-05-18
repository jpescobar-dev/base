<?php

namespace App\Services\Contractual;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class PdfTextExtractorService
{
    public function extractFromPublicPath(string $relativePath): array
    {
        $absolutePath = Storage::disk('public')->path($relativePath);

        if (!file_exists($absolutePath)) {
            return [
                'texto' => null,
                'estado' => 'ERROR_EXTRACCION',
                'tiene_texto_extraible' => false,
                'mensaje' => 'El archivo no existe en disco.',
            ];
        }

        try {
            $binary = (string) config('ocr.pdftotext_binary');

            $texto = Pdf::getText($absolutePath, $binary);
            $texto = is_string($texto) ? trim($texto) : null;

            if (!$texto || mb_strlen($texto) < 30) {
                return [
                    'texto' => null,
                    'estado' => 'SIN_TEXTO',
                    'tiene_texto_extraible' => false,
                    'mensaje' => 'No fue posible extraer texto útil del PDF.',
                ];
            }

            return [
                'texto' => $texto,
                'estado' => 'EXTRAIDO',
                'tiene_texto_extraible' => true,
                'mensaje' => 'Texto extraído correctamente.',
            ];
        } catch (\Throwable $e) {
            Log::error('Error en extracción pdftotext', [
                'relative_path' => $relativePath,
                'absolute_path' => $absolutePath,
                'binary' => config('ocr.pdftotext_binary'),
                'mensaje' => $e->getMessage(),
            ]);

            return [
                'texto' => null,
                'estado' => 'ERROR_EXTRACCION',
                'tiene_texto_extraible' => false,
                'mensaje' => $e->getMessage(),
            ];
        }
    }
}