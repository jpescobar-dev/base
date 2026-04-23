<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumentoContractual;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TipoDocumentoContractualController extends Controller
{
    public function index(): View
    {
        $tipos = TipoDocumentoContractual::orderBy('peso_jerarquico')->get();

        return view('contractual.tipos_documento.index', compact('tipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:80', 'unique:tipos_documento_contractual,codigo'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'jerarquia' => ['required', 'in:alta,media,baja'],
            'peso_jerarquico' => ['required', 'integer', 'min:1', 'max:999'],
            'es_critico' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ]);

        TipoDocumentoContractual::create([
            ...$data,
            'es_critico' => (bool) $request->boolean('es_critico'),
            'activo' => $request->has('activo') ? (bool) $request->boolean('activo') : true,
        ]);

        return back()->with('success', 'Tipo de documento creado correctamente.');
    }

    public function update(Request $request, TipoDocumentoContractual $tipo): RedirectResponse
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:80', 'unique:tipos_documento_contractual,codigo,' . $tipo->id],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'jerarquia' => ['required', 'in:alta,media,baja'],
            'peso_jerarquico' => ['required', 'integer', 'min:1', 'max:999'],
            'es_critico' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $tipo->update([
            ...$data,
            'es_critico' => (bool) $request->boolean('es_critico'),
            'activo' => (bool) $request->boolean('activo'),
        ]);

        return back()->with('success', 'Tipo de documento actualizado correctamente.');
    }
}
