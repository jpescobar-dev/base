<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use App\Services\Contractual\ExportRevisionContractualWordService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportRevisionContractualController extends Controller
{
    public function snapshot(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot,
        ExportRevisionContractualWordService $service
    ): BinaryFileResponse {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        $snapshot->load(['hallazgos', 'checklist']);

        $filePath = $service->exportSnapshot($revision, $snapshot);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function compare(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot1,
        SnapshotRevisionContractual $snapshot2,
        ExportRevisionContractualWordService $service
    ): BinaryFileResponse {
        if (
            (int) $snapshot1->revision_contractual_id !== (int) $revision->id ||
            (int) $snapshot2->revision_contractual_id !== (int) $revision->id
        ) {
            abort(404);
        }

        $snapshot1->load(['hallazgos', 'checklist']);
        $snapshot2->load(['hallazgos', 'checklist']);

        $filePath = $service->exportComparison($revision, $snapshot1, $snapshot2);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
