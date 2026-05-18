<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Models\RevisionTipoDocumento;
use App\Models\TipoDocumentoContractual;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RevisionTipoDocumentoController extends Controller
{
    public function edit(RevisionContractual $revision): View
    {
        $tipos = TipoDocumentoContractual::where('activo', true)
            ->orderBy('peso_jerarquico')
            ->get();

        $configurados = $revision->tiposDocumentoConfigurados()
            ->with('tipo')
            ->get()
            ->keyBy('tipo_documento_contractual_id');

        return view('contractual.revisiones.configurar_documentos', compact('revision', 'tipos', 'configurados'));
    }

    public function update(Request $request, RevisionContractual $revision): RedirectResponse
    {
        $tipos = TipoDocumentoContractual::where('activo', true)->get();

        foreach ($tipos as $tipo) {
            $aplica = $request->boolean('aplica_' . $tipo->id);
            $obligatorio = $request->boolean('obligatorio_' . $tipo->id);
            $observacion = $request->input('observacion_' . $tipo->id);

            RevisionTipoDocumento::updateOrCreate(
                [
                    'revision_contractual_id' => $revision->id,
                    'tipo_documento_contractual_id' => $tipo->id,
                ],
                [
                    'aplica' => $aplica,
                    'obligatorio' => $aplica ? $obligatorio : false,
                    'observacion' => $observacion,
                    'user_id' => auth()->id(),
                ]
            );
        }

        return redirect()
            ->route('contractual.revisiones.show', $revision)
            ->with('success', 'Configuración documental guardada correctamente.');
    }
}
