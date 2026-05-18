@extends('layouts.theme.app')

@section('title', 'Tipos de Documento')
@section('title2', 'Tipos documentales')

@section('content')
<div class="container-fluid">
    <div class="widget-content widget-content-area br-6 mt-2 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Catálogo de tipos documentales</h4>
                <div class="text-muted">Jerarquización y criticidad base del sistema.</div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Nuevo tipo documental</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('contractual.tipos-documento.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Jerarquía</label>
                            <select name="jerarquia" class="form-control" required>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Peso</label>
                            <input type="number" name="peso_jerarquico" class="form-control" value="50" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label d-block">Opciones</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="es_critico" value="1" id="nuevo_critico">
                                <label class="form-check-label" for="nuevo_critico">Crítico</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" id="nuevo_activo" checked>
                                <label class="form-check-label" for="nuevo_activo">Activo</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar tipo documental</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white"><strong>Tipos existentes</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Jerarquía</th>
                                <th>Peso</th>
                                <th>Crítico</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipos as $tipo)
                                <tr>
                                    <td>{{ $tipo->id }}</td>
                                    <td>{{ $tipo->codigo }}</td>
                                    <td>{{ $tipo->nombre }}</td>
                                    <td>{{ strtoupper($tipo->jerarquia) }}</td>
                                    <td>{{ $tipo->peso_jerarquico }}</td>
                                    <td>{{ $tipo->es_critico ? 'Sí' : 'No' }}</td>
                                    <td>{{ $tipo->activo ? 'Sí' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No existen tipos documentales.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
