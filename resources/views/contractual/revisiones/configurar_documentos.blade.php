@extends('layouts.theme.app')

@section('title', 'Configuración Documental')
@section('title2', 'Revisión')

@section('content')
<div class="container-fluid">
    <div class="widget-content widget-content-area br-6 mt-2 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Configuración documental de la revisión</h4>
                <div class="text-muted">Revisión #{{ $revision->id }} · {{ $revision->titulo }}</div>
            </div>

            <a href="{{ route('contractual.revisiones.show', $revision) }}" class="btn btn-outline-secondary btn-sm">
                Volver
            </a>
        </div>

        <div class="alert alert-light border">
            Marca qué tipos documentales aplican a este expediente y cuáles son obligatorios.
            La IA solo debería analizar con base en esta configuración.
        </div>

        <form method="POST" action="{{ route('contractual.revisiones.configuracion-documental.update', $revision) }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm">
                <div class="card-header bg-white"><strong>Tipos documentales aplicables</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Jerarquía</th>
                                    <th>Peso</th>
                                    <th>Crítico</th>
                                    <th>Aplica</th>
                                    <th>Obligatorio</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipos as $tipo)
                                    @php
                                        $cfg = $configurados->get($tipo->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="font-weight-semibold">{{ $tipo->nombre }}</div>
                                            <div class="small text-muted">{{ $tipo->codigo }}</div>
                                        </td>
                                        <td>{{ strtoupper($tipo->jerarquia) }}</td>
                                        <td>{{ $tipo->peso_jerarquico }}</td>
                                        <td>{{ $tipo->es_critico ? 'Sí' : 'No' }}</td>
                                        <td class="text-center">
                                            <input type="checkbox" name="aplica_{{ $tipo->id }}" value="1"
                                                {{ old('aplica_' . $tipo->id, $cfg->aplica ?? false) ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="obligatorio_{{ $tipo->id }}" value="1"
                                                {{ old('obligatorio_' . $tipo->id, $cfg->obligatorio ?? false) ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="observacion_{{ $tipo->id }}"
                                                   class="form-control form-control-sm"
                                                   value="{{ old('observacion_' . $tipo->id, $cfg->observacion ?? '') }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Guardar configuración documental
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
