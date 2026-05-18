<?php

namespace App\Services\Contractual;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfOcrExtractorService
{
    public function extractFromPublicPath(string $relativePath): array
    {
        if (!config('ocr.enabled')) {
            return [
                'texto' => null,
                'estado' => 'OCR_DESHABILITADO',
                'mensaje' => 'OCR deshabilitado por configuración.',
            ];
        }

        $absolutePath = Storage::disk('public')->path($relativePath);

        if (!file_exists($absolutePath)) {
            return [
                'texto' => null,
                'estado' => 'OCR_ERROR',
                'mensaje' => 'El archivo no existe en disco.',
            ];
        }

        $tempRoot = rtrim((string) config('ocr.temp_dir'), DIRECTORY_SEPARATOR);
        $workDir = $tempRoot . DIRECTORY_SEPARATOR . 'doc_' . uniqid();

        if (!is_dir($workDir)) {
            mkdir($workDir, 0777, true);
        }

        try {
            $pdftoppm = (string) config('ocr.pdftoppm_binary', 'pdftoppm');
            $tesseract = (string) config('ocr.tesseract_binary', 'tesseract');
            $language = (string) config('ocr.language', 'spa');
            $dpi = (int) config('ocr.dpi', 300);
            $maxPages = (int) config('ocr.max_pages', 20);

            $imgPrefix = $workDir . DIRECTORY_SEPARATOR . 'page';

            $cmdRender = sprintf(
                '"%s" -r %d -png %s %s',
                $pdftoppm,
                $dpi,
                escapeshellarg($absolutePath),
                escapeshellarg($imgPrefix)
            );

            exec($cmdRender . ' 2>&1', $renderOutput, $renderCode);

            if ($renderCode !== 0) {
                return [
                    'texto' => null,
                    'estado' => 'OCR_ERROR_RENDER',
                    'mensaje' => implode(PHP_EOL, $renderOutput),
                ];
            }

            $images = glob($workDir . DIRECTORY_SEPARATOR . 'page-*.png') ?: [];

            if (empty($images)) {
                return [
                    'texto' => null,
                    'estado' => 'OCR_SIN_IMAGENES',
                    'mensaje' => 'No se generaron imágenes desde el PDF.',
                ];
            }

            natsort($images);
            $images = array_slice(array_values($images), 0, $maxPages);

            $bloques = [];

            foreach ($images as $index => $imagePath) {
                $outputBase = $workDir . DIRECTORY_SEPARATOR . 'ocr_' . ($index + 1);

               $cmdOcr = sprintf(
                '"%s" %s %s -l %s',
                $tesseract,
                escapeshellarg($imagePath),
                escapeshellarg($outputBase),
                $language
            );

                exec($cmdOcr . ' 2>&1', $ocrOutput, $ocrCode);

                if ($ocrCode !== 0) {
                    Log::warning('OCR falló en página', [
                        'image' => $imagePath,
                        'output' => $ocrOutput,
                    ]);
                    continue;
                }

                $txtFile = $outputBase . '.txt';

                if (!file_exists($txtFile)) {
                    continue;
                }

                $textoPagina = trim((string) file_get_contents($txtFile));

                if ($textoPagina !== '') {
                    $bloques[] = $textoPagina;
                }
            }

            $textoFinal = trim(implode(PHP_EOL . PHP_EOL, $bloques));

            if ($textoFinal === '' || mb_strlen($textoFinal) < 30) {
                return [
                    'texto' => null,
                    'estado' => 'OCR_SIN_TEXTO',
                    'mensaje' => 'OCR ejecutado, pero sin texto útil.',
                ];
            }

            return [
                'texto' => $textoFinal,
                'estado' => 'OCR_EXTRAIDO',
                'mensaje' => 'Texto OCR extraído correctamente.',
            ];
        } catch (\Throwable $e) {
            return [
                'texto' => null,
                'estado' => 'OCR_ERROR',
                'mensaje' => $e->getMessage(),
            ];
        } finally {
            $this->deleteDirectory($workDir);
        }
    }

    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } elseif (file_exists($path)) {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
