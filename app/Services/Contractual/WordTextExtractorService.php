<?php

namespace App\Services\Contractual;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

class WordTextExtractorService
{
    public function extractFromPublicPath(string $relativePath): array
    {
        $absolutePath = Storage::disk('public')->path($relativePath);

        if (!file_exists($absolutePath)) {
            return [
                'texto' => null,
                'estado' => 'ERROR_EXTRACCION',
                'mensaje' => 'El archivo no existe en disco.',
            ];
        }

        try {
            $phpWord = IOFactory::load($absolutePath);
            $partes = [];

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof Text) {
                        $partes[] = $element->getText();
                    } elseif ($element instanceof TextRun) {
                        foreach ($element->getElements() as $inner) {
                            if ($inner instanceof Text) {
                                $partes[] = $inner->getText();
                            } elseif (method_exists($inner, 'getText')) {
                                $partes[] = (string) $inner->getText();
                            }
                        }
                    } elseif (method_exists($element, 'getText')) {
                        $partes[] = (string) $element->getText();
                    }
                }
            }

            $texto = trim(implode(PHP_EOL, array_filter($partes, fn ($v) => trim((string) $v) !== '')));

            if ($texto === '' || mb_strlen($texto) < 20) {
                return [
                    'texto' => null,
                    'estado' => 'SIN_TEXTO',
                    'mensaje' => 'No fue posible extraer texto útil del archivo Word.',
                ];
            }

            return [
                'texto' => $texto,
                'estado' => 'EXTRAIDO',
                'mensaje' => 'Texto Word extraído correctamente.',
            ];
        } catch (\Throwable $e) {
            return [
                'texto' => null,
                'estado' => 'ERROR_EXTRACCION',
                'mensaje' => $e->getMessage(),
            ];
        }
    }
}
