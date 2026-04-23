<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use Illuminate\Support\Collection;

class DocumentContradictionDetectorService
{
    public function detect(RevisionContractual $revision): array
    {
        $revision->load(['documentos.tipoDocumento' => function ($q) {
            $q->orderBy('peso_jerarquico');
        }]);

        $docs = $revision->documentos->map(function ($doc) {
            $texto = trim((string) ($doc->texto_extraido ?: $doc->texto_ocr ?: ''));

            return [
                'id' => $doc->id,
                'nombre_original' => $doc->nombre_original,
                'tipo_documento' => $doc->tipoDocumento->codigo ?? $doc->tipo_documento ?? 'OTRO',
                'tipo_documento_nombre' => $doc->tipoDocumento->nombre ?? $doc->tipo_documento ?? 'Otro',
                'peso_jerarquico' => $doc->tipoDocumento->peso_jerarquico ?? 0,
                'jerarquia' => $doc->tipoDocumento->jerarquia ?? 'baja',
                'texto' => $texto,
                'proveedor' => $this->extractProveedor($texto),
                'plazo_meses' => $this->extractPlazoMeses($texto),
                'monto_uf' => $this->extractMontoUf($texto),
                'garantia_uf' => $this->extractGarantiaUf($texto),
                'fecha_inicio' => $this->extractFechaInicio($texto),
                'id_licitacion' => $this->extractIdLicitacion($texto),
            ];
        })->filter(fn ($doc) => $doc['texto'] !== '')->values();

        $contradicciones = $this->buildContradictions($docs);

        return [
            'resumen' => [
                'documentos_analizados' => $docs->count(),
                'contradicciones_detectadas' => count($contradicciones),
            ],
            'contradicciones' => $contradicciones,
        ];
    }

    protected function buildContradictions(Collection $docs): array
    {
        $contradicciones = [];

        $campos = [
            'id_licitacion' => 'ID licitación',
            'proveedor' => 'Proveedor',
            'plazo_meses' => 'Plazo en meses',
            'monto_uf' => 'Monto UF',
            'garantia_uf' => 'Garantía UF',
            'fecha_inicio' => 'Fecha de inicio',
        ];

        foreach ($campos as $campo => $label) {
            $valores = $docs
                ->filter(fn ($doc) => !empty($doc[$campo]))
                ->map(function ($doc) use ($campo) {
                    return [
                        'documento_id' => $doc['id'],
                        'documento' => $doc['nombre_original'],
                        'tipo_documento' => $doc['tipo_documento'],
                        'tipo_documento_nombre' => $doc['tipo_documento_nombre'],
                        'valor' => $doc[$campo],
                        'peso_jerarquico' => $doc['peso_jerarquico'],
                        'jerarquia' => $doc['jerarquia'],
                    ];
                })
                ->values();

            $unicos = $valores->pluck('valor')->unique()->values();

            if ($unicos->count() > 1) {
                $prevalente = $this->resolvePrevalentDocument($valores);

                $contradicciones[] = [
                    'campo' => $campo,
                    'etiqueta' => $label,
                    'criticidad' => in_array($campo, ['id_licitacion', 'plazo_meses', 'monto_uf', 'garantia_uf']) ? 'alta' : 'media',
                    'descripcion' => "Se detectaron valores distintos para {$label} entre documentos del expediente.",
                    'valores' => $valores->all(),
                    'documento_prevalente' => $prevalente['documento_prevalente'],
                    'tipo_documento_prevalente' => $prevalente['tipo_documento_prevalente'],
                    'peso_prevalente' => $prevalente['peso_prevalente'],
                    'valor_prevalente' => $prevalente['valor_prevalente'],
                    'requiere_verificacion_manual' => $prevalente['requiere_verificacion_manual'],
                    'motivo_prevalencia' => $prevalente['motivo_prevalencia'],
                    'recomendacion' => $prevalente['recomendacion'],
                ];
            }
        }

        return $contradicciones;
    }

    protected function resolvePrevalentDocument(Collection $valores): array
    {
        $ordenados = $valores->sortByDesc('peso_jerarquico')->values();
        $primero = $ordenados->get(0);
        $segundo = $ordenados->get(1);

        if (!$primero) {
            return [
                'documento_prevalente' => null,
                'tipo_documento_prevalente' => null,
                'peso_prevalente' => null,
                'valor_prevalente' => null,
                'requiere_verificacion_manual' => true,
                'motivo_prevalencia' => 'No fue posible determinar documento prevalente.',
                'recomendacion' => 'Verificar manualmente la contradicción documental.',
            ];
        }

        if ($segundo && (int) $primero['peso_jerarquico'] === (int) $segundo['peso_jerarquico']) {
            return [
                'documento_prevalente' => null,
                'tipo_documento_prevalente' => null,
                'peso_prevalente' => (int) $primero['peso_jerarquico'],
                'valor_prevalente' => null,
                'requiere_verificacion_manual' => true,
                'motivo_prevalencia' => 'Existen documentos con igual peso jerárquico en conflicto.',
                'recomendacion' => 'Pendiente de verificar. No es posible establecer prevalencia automática por empate jerárquico.',
            ];
        }

        return [
            'documento_prevalente' => $primero['documento'],
            'tipo_documento_prevalente' => $primero['tipo_documento_nombre'],
            'peso_prevalente' => (int) $primero['peso_jerarquico'],
            'valor_prevalente' => $primero['valor'],
            'requiere_verificacion_manual' => false,
            'motivo_prevalencia' => 'Prevalece preliminarmente el documento con mayor peso jerárquico.',
            'recomendacion' => 'Tomar como referencia preliminar el valor del documento de mayor jerarquía, sin perjuicio de validación jurídica o administrativa.',
        ];
    }

    protected function extractProveedor(string $texto): ?string
    {
        $patterns = [
            '/proveedor\s*:\s*([^\n\r]+)/iu',
            '/contratista\s*:\s*([^\n\r]+)/iu',
            '/adjudicatario\s*:\s*([^\n\r]+)/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $texto, $m)) {
                return $this->normalizeInline($m[1]);
            }
        }

        return null;
    }

    protected function extractPlazoMeses(string $texto): ?string
    {
        if (preg_match('/(\d{1,3})\s*mes(?:es)?/iu', $texto, $m)) {
            return (string) $m[1];
        }

        return null;
    }

    protected function extractMontoUf(string $texto): ?string
    {
        if (preg_match('/(\d{1,3}(?:[\.,]\d{3})*(?:[\.,]\d{1,2})?)\s*UF/iu', $texto, $m)) {
            return $this->normalizeNumber($m[1]);
        }

        return null;
    }

    protected function extractGarantiaUf(string $texto): ?string
    {
        $patterns = [
            '/garant[íi]a[^\n\r]{0,80}?(\d{1,3}(?:[\.,]\d{3})*(?:[\.,]\d{1,2})?)\s*UF/iu',
            '/fiel cumplimiento[^\n\r]{0,80}?(\d{1,3}(?:[\.,]\d{3})*(?:[\.,]\d{1,2})?)\s*UF/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $texto, $m)) {
                return $this->normalizeNumber($m[1]);
            }
        }

        return null;
    }

    protected function extractFechaInicio(string $texto): ?string
    {
        $patterns = [
            '/inicio(?:\s+del\s+servicio)?\s*:\s*(\d{2}[-\/]\d{2}[-\/]\d{4})/iu',
            '/desde\s+el\s+(\d{2}[-\/]\d{2}[-\/]\d{4})/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $texto, $m)) {
                return str_replace('/', '-', $m[1]);
            }
        }

        return null;
    }

    protected function extractIdLicitacion(string $texto): ?string
    {
        if (preg_match('/\b(\d{4}-\d{1,4}-[A-Z]{2}\d{2})\b/u', $texto, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }

    protected function normalizeInline(string $value): string
    {
        $value = preg_replace('/\s+/', ' ', trim($value));
        return mb_substr($value, 0, 160);
    }

    protected function normalizeNumber(string $value): string
    {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return trim($value);
    }
}
