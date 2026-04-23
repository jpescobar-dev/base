<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use App\Services\Contractual\RevisionAnalysisGateService;
use App\Services\Contractual\RevisionReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class RevisionReportController extends Controller
{
    public function show(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot,
        RevisionReportService $reportService,
        RevisionAnalysisGateService $gateService
    ): View {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        $gate = $gateService->evaluate($revision);
        $report = $reportService->build($revision, $snapshot, $gate);

        return view('contractual.reportes.show', compact('revision', 'snapshot', 'report'));
    }

    public function exportWord(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot,
        RevisionReportService $reportService,
        RevisionAnalysisGateService $gateService
    ): Response {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        $gate = $gateService->evaluate($revision);
        $report = $reportService->build($revision, $snapshot, $gate);

        $html = view('contractual.reportes.word', compact('revision', 'snapshot', 'report'))->render();

        $filename = 'informe_revision_contractual_v' . $snapshot->numero_version . '.doc';

        return response($html, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
