<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ExportRevisionContractualWordService
{
    public function exportSnapshot(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): string
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $this->addTitle($section, 'Informe de Snapshot de Revisión Contractual');
        $this->addMeta($section, $revision, $snapshot);

        $resumen = is_array($snapshot->json_resultado) ? ($snapshot->json_resultado['resumen'] ?? []) : [];

        $this->addHeading($section, '1. Resumen');
        $section->addText('Tipo de contrato: ' . ($resumen['tipo_contrato'] ?? '-'));
        $section->addText('Entidad: ' . ($resumen['entidad'] ?? '-'));
        $section->addText('Riesgo general: ' . strtoupper((string) ($resumen['riesgo_general'] ?? '-')));

        if (!empty($resumen['observaciones_clave']) && is_array($resumen['observaciones_clave'])) {
            $section->addText('Observaciones clave:');
            foreach ($resumen['observaciones_clave'] as $obs) {
                $section->addListItem((string) $obs, 0);
            }
        }

        $this->addHeading($section, '2. Hallazgos');
        foreach ($snapshot->hallazgos as $hallazgo) {
            $section->addText($hallazgo->titulo ?: 'Hallazgo', ['bold' => true]);
            $section->addText('Tipo de riesgo: ' . ($hallazgo->tipo_riesgo ?? '-'));
            $section->addText('Criticidad: ' . strtoupper((string) ($hallazgo->nivel_criticidad ?? '-')));
            $section->addText('Descripción: ' . ($hallazgo->observacion ?? '-'));
            $section->addText('Recomendación: ' . ($hallazgo->recomendacion ?? '-'));
            $section->addTextBreak(1);
        }

        $this->addHeading($section, '3. Checklist');
        $checklistPorCapa = $snapshot->checklist->groupBy(fn ($item) => $item->tipo_checklist ?: 'general');

        foreach ($checklistPorCapa as $capa => $items) {
            $section->addText(ucfirst((string) $capa), ['bold' => true]);
            foreach ($items as $item) {
                $section->addText(
                    '[' . strtoupper((string) $item->estado_item) . '] ' . $item->item . ' - ' . ($item->observacion ?? '-')
                );
            }
            $section->addTextBreak(1);
        }

        return $this->save($phpWord, 'snapshot_revision_' . $revision->id . '_v' . $snapshot->numero_version . '.docx');
    }

    public function exportComparison(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot1,
        SnapshotRevisionContractual $snapshot2
    ): string {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $this->addTitle($section, 'Informe Comparativo de Snapshots');
        $section->addText('Revisión: #' . $revision->id);
        $section->addText('Snapshot base: versión ' . $snapshot1->numero_version);
        $section->addText('Snapshot comparado: versión ' . $snapshot2->numero_version);
        $section->addTextBreak(1);

        $hallazgos1 = $snapshot1->hallazgos->keyBy(fn ($h) => mb_strtolower(trim((string) $h->titulo)));
        $hallazgos2 = $snapshot2->hallazgos->keyBy(fn ($h) => mb_strtolower(trim((string) $h->titulo)));

        $nuevos = $hallazgos2->filter(fn ($h, $k) => !$hallazgos1->has($k));
        $eliminados = $hallazgos1->filter(fn ($h, $k) => !$hallazgos2->has($k));

        $this->addHeading($section, '1. Resumen ejecutivo');
        $section->addText('Hallazgos nuevos: ' . $nuevos->count());
        $section->addText('Hallazgos eliminados: ' . $eliminados->count());
        $section->addText('Checklist snapshot base: ' . $snapshot1->checklist->count());
        $section->addText('Checklist snapshot comparado: ' . $snapshot2->checklist->count());
        $section->addTextBreak(1);

        $this->addHeading($section, '2. Hallazgos nuevos');
        foreach ($nuevos as $hallazgo) {
            $section->addListItem($hallazgo->titulo ?: 'Hallazgo sin título', 0);
        }
        if ($nuevos->isEmpty()) {
            $section->addText('No se detectan hallazgos nuevos.');
        }

        $section->addTextBreak(1);
        $this->addHeading($section, '3. Hallazgos eliminados');
        foreach ($eliminados as $hallazgo) {
            $section->addListItem($hallazgo->titulo ?: 'Hallazgo sin título', 0);
        }
        if ($eliminados->isEmpty()) {
            $section->addText('No se detectan hallazgos eliminados.');
        }

        $section->addTextBreak(1);
        $this->addHeading($section, '4. Cambios en checklist');
        $checklist1 = $snapshot1->checklist->keyBy(fn ($c) => mb_strtolower(trim((string) $c->tipo_checklist . '|' . $c->item)));
        $checklist2 = $snapshot2->checklist->keyBy(fn ($c) => mb_strtolower(trim((string) $c->tipo_checklist . '|' . $c->item)));

        $changes = 0;
        foreach ($checklist2 as $key => $nuevo) {
            if (!$checklist1->has($key)) {
                $section->addText('[NUEVO] ' . $nuevo->item . ' | Estado: ' . $nuevo->estado_item);
                $changes++;
                continue;
            }

            $anterior = $checklist1->get($key);

            if ((string) $anterior->estado_item !== (string) $nuevo->estado_item) {
                $section->addText(
                    '[CAMBIO] ' . $nuevo->item .
                    ' | Antes: ' . $anterior->estado_item .
                    ' | Después: ' . $nuevo->estado_item
                );
                $changes++;
            }
        }

        if ($changes === 0) {
            $section->addText('No se detectan cambios relevantes en checklist.');
        }

        return $this->save($phpWord, 'comparacion_revision_' . $revision->id . '_v' . $snapshot1->numero_version . '_vs_v' . $snapshot2->numero_version . '.docx');
    }

    protected function addTitle($section, string $text): void
    {
        $section->addText($text, ['bold' => true, 'size' => 16]);
        $section->addTextBreak(1);
    }

    protected function addHeading($section, string $text): void
    {
        $section->addText($text, ['bold' => true, 'size' => 12]);
    }

    protected function addMeta($section, RevisionContractual $revision, SnapshotRevisionContractual $snapshot): void
    {
        $section->addText('Revisión: #' . $revision->id);
        $section->addText('Título: ' . ($revision->titulo ?? '-'));
        $section->addText('Snapshot versión: ' . $snapshot->numero_version);
        $section->addText('Fecha de generación: ' . optional($snapshot->created_at)->format('d-m-Y H:i'));
        $section->addTextBreak(1);
    }

    protected function save(PhpWord $phpWord, string $fileName): string
    {
        $dir = storage_path('app/tmp_exports');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return $filePath;
    }
}
