<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use App\Services\Contractual\ContradiccionComparatorService;
use Illuminate\Contracts\View\View;

class ComparadorContradiccionesController extends Controller
{
    public function compare(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot1,
        SnapshotRevisionContractual $snapshot2,
        ContradiccionComparatorService $service
    ): View {
        if (
            (int) $snapshot1->revision_contractual_id !== (int) $revision->id ||
            (int) $snapshot2->revision_contractual_id !== (int) $revision->id
        ) {
            abort(404);
        }

        $snapshot1->load(['contradicciones']);
        $snapshot2->load(['contradicciones']);

        $comparacion = $service->compare($snapshot2, $snapshot1);
        $snapshots = $revision->snapshots()->orderBy('numero_version', 'desc')->get();

        return view('contractual.snapshots.comparador_contradicciones', [
            'revision' => $revision,
            'snapshot1' => $snapshot1,
            'snapshot2' => $snapshot2,
            'snapshots' => $snapshots,
            'comparacion' => $comparacion,
        ]);
    }
}
