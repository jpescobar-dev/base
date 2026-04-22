<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractual\StoreDocumentoRevisionContractualRequest;
use App\Models\DocumentoRevisionContractual;
use App\Models\RevisionContractual;
use App\Services\Contractual\DocumentoTextPipelineService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentoRevisionContractualController extends Controller
{
    public function index(RevisionContractual $revision): View
    {
        $revision->load(['documentos.usuario']);

        return view('contractual.documentos.index', compact('revision'));
    }

    public function show(RevisionContractual $revision, DocumentoRevisionContractual $documento)
    {
        if ((int) $documento->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        if (!$documento->ruta || !Storage::disk('public')->exists($documento->ruta)) {
            abort(404, 'El archivo no existe en disco.');
        }

        $extension = strtolower((string) $documento->extension);
        $absolutePath = Storage::disk('public')->path($documento->ruta);

        if ($extension === 'pdf') {
            return view('contractual.documentos.show', compact('revision', 'documento'));
        }

        return response()->download(
            $absolutePath,
            $documento->nombre_original,
            ['Content-Type' => $documento->mime_type ?: mime_content_type($absolutePath)]
        );
    }

    public function preview(RevisionContractual $revision, DocumentoRevisionContractual $documento): BinaryFileResponse
    {
        if ((int) $documento->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        if (!$documento->ruta || !Storage::disk('public')->exists($documento->ruta)) {
            abort(404, 'El archivo no existe en disco.');
        }

        $absolutePath = Storage::disk('public')->path($documento->ruta);

        return response()->file($absolutePath, [
            'Content-Type' => $documento->mime_type ?: mime_content_type($absolutePath),
            'Content-Disposition' => 'inline; filename="' . ($documento->nombre_original ?: basename($absolutePath)) . '"',
        ]);
    }

    public function download(RevisionContractual $revision, DocumentoRevisionContractual $documento): BinaryFileResponse
    {
        if ((int) $documento->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        if (!$documento->ruta || !Storage::disk('public')->exists($documento->ruta)) {
            abort(404, 'El archivo no existe en disco.');
        }

        $absolutePath = Storage::disk('public')->path($documento->ruta);

        return response()->download(
            $absolutePath,
            $documento->nombre_original,
            ['Content-Type' => $documento->mime_type ?: mime_content_type($absolutePath)]
        );
    }

    public function store(
        StoreDocumentoRevisionContractualRequest $request,
        RevisionContractual $revision,
        DocumentoTextPipelineService $pipelineService
    ): RedirectResponse {
        $archivo = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());

        if (!in_array($extension, ['pdf', 'docx'])) {
            return back()->with('error', 'Formato no soportado. Solo PDF o DOCX.');
        }

        $ruta = $archivo->store('contractual/documentos', 'public');
        $hash = hash_file('sha256', $archivo->getRealPath());

        $existe = DocumentoRevisionContractual::where('hash_archivo', $hash)->first();

        if ($existe) {
            Storage::disk('public')->delete($ruta);

            return back()->with('warning', 'El documento ya fue cargado anteriormente y no se duplicó.');
        }

        $resultado = $pipelineService->process($ruta, $extension);

        DocumentoRevisionContractual::create([
            'revision_contractual_id' => $revision->id,
            'nombre_original' => $archivo->getClientOriginalName(),
            'ruta' => $ruta,
            'mime_type' => $archivo->getClientMimeType(),
            'extension' => $extension,
            'peso_bytes' => $archivo->getSize(),
            'hash_archivo' => $hash,
            'tipo_documento' => $request->input('tipo_documento'),
            'texto_extraido' => $resultado['texto_extraido'],
            'texto_ocr' => $resultado['texto_ocr'],
            'extraccion_estado' => $resultado['extraccion_estado'],
            'ocr_estado' => $resultado['ocr_estado'],
            'tiene_texto_extraible' => $resultado['tiene_texto_extraible'],
            'fuente_texto' => $resultado['fuente_texto'],
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', $resultado['mensaje']);
    }

    public function destroy(RevisionContractual $revision, DocumentoRevisionContractual $documento): RedirectResponse
    {
        if ((int) $documento->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->delete($documento->ruta);
        }

        $documento->delete();

        return back()->with('success', 'Documento eliminado correctamente.');
    }
}
