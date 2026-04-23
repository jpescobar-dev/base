@extends('layouts.theme.app')

@section('title', 'Informe de Revisión')
@section('title2', 'Informe')

@section('content')
@php
    $gate = $report['gate'] ?? [];
    $resumen = $report['resumen'] ?? [];
@endphp

<div class="container-fluid">
    <div class="widget-content widget-content-area br-6 mt-2 mb-2">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="mb-1">Informe de revisión contractual</h4>
                <div class="text-muted">Revisión #{{ $revision->id }} · Snapshot v{{ $snapshot->numero_version }}</div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot]) }}" class="btn btn-outline-secondary btn-sm">
                    Volver
                </a>
                <a href="{{ route('contractual.revisiones.reportes.export-word', [$revision, $snapshot]) }}" class="btn btn-outline-primary btn-sm">
                    Exportar Word
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Tipo contrato</div>
                    <div class="font-weight-bold">{{ $resumen['tipo_contrato'] ?? '-' }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Entidad</div>
                    <div class="font-weight-bold">{{ $resumen['entidad'] ?? '-' }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Riesgo general</div>
                    <div class="font-weight-bold">{{ strtoupper($resumen['riesgo_general'] ?? '-') }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body">
                    <div class="small text-muted">Estado análisis</div>
                    <div class="font-weight-bold">{{ ($gate['estado'] ?? 'no_apto') === 'apto' ? 'APTO' : 'NO APTO' }}</div>
                </div></div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Estado documental</strong></div>
            <div class="card-body">
                <p><strong>Obligatorios configurados:</strong> {{ $gate['total_obligatorios'] ?? 0 }}</p>
                <p><strong>Críticos presentes:</strong> {{ $gate['criticos_presentes'] ?? 0 }}</p>
                @if(!empty($gate['faltantes']))
                    <p class="text-danger"><strong>Faltantes:</strong> {{ implode(', ', $gate['faltantes']) }}</p>
                @endif
                @if(!empty($gate['mensajes']))
                    <ul class="mb-0">
                        @foreach($gate['mensajes'] as $m)
                            <li>{{ $m }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Resumen ejecutivo</strong></div>
            <div class="card-body">
                <ul class="mb-0">
                    @forelse(($resumen['observaciones_clave'] ?? []) as $obs)
                        <li>{{ $obs }}</li>
                    @empty
                        <li class="text-muted">No existen observaciones clave registradas.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Contradicciones con prevalencia</strong></div>
            <div class="card-body">
                @forelse(($report['contradicciones'] ?? []) as $c)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <strong>{{ $c['etiqueta'] ?? '-' }}</strong>
                            <span class="badge {{ ($c['criticidad'] ?? '') === 'alta' ? 'badge-danger' : 'badge-warning' }}">
                                {{ strtoupper($c['criticidad'] ?? '-') }}
                            </span>
                        </div>
                        <p>{{ $c['descripcion'] ?? '-' }}</p>
                        <ul>
                            @foreach(($c['valores'] ?? []) as $v)
                                <li>{{ $v['documento'] ?? 'Documento' }} ({{ $v['tipo_documento_nombre'] ?? '-' }}, peso {{ $v['peso_jerarquico'] ?? 0 }}): {{ $v['valor'] ?? '-' }}</li>
                            @endforeach
                        </ul>
                        <p><strong>Documento prevalente:</strong> {{ $c['documento_prevalente'] ?? 'Pendiente de verificar' }}</p>
                        <p><strong>Motivo de prevalencia:</strong> {{ $c['motivo_prevalencia'] ?? '-' }}</p>
                        <p><strong>Recomendación:</strong> {{ $c['recomendacion'] ?? '-' }}</p>
                    </div>
                @empty
                    <div class="alert alert-light border mb-0">No hay contradicciones registradas.</div>
                @endforelse
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Hallazgos</strong></div>
            <div class="card-body">
                @forelse(($report['hallazgos'] ?? []) as $h)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <strong>{{ $h['titulo'] ?? '-' }}</strong>
                            <span class="badge {{ ($h['criticidad'] ?? '') === 'alta' ? 'badge-danger' : (($h['criticidad'] ?? '') === 'media' ? 'badge-warning' : 'badge-success') }}">
                                {{ strtoupper($h['criticidad'] ?? '-') }}
                            </span>
                        </div>
                        <p><strong>Tipo de riesgo:</strong> {{ $h['tipo_riesgo'] ?? '-' }}</p>
                        <p><strong>Observación:</strong> {{ $h['observacion'] ?? '-' }}</p>
                        <p class="mb-0"><strong>Recomendación:</strong> {{ $h['recomendacion'] ?? '-' }}</p>
                    </div>
                @empty
                    <div class="alert alert-light border mb-0">No hay hallazgos registrados.</div>
                @endforelse
            </div>
        </div>

        @foreach(['existencia' => 'Existencia', 'coherencia' => 'Coherencia', 'cumplimiento' => 'Cumplimiento'] as $k => $titulo)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Checklist - {{ $titulo }}</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Ítem</th>
                                <th>Estado</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($report['checklist'][$k] ?? []) as $item)
                                <tr>
                                    <td>{{ $item['item'] ?? '-' }}</td>
                                    <td>{{ strtoupper($item['estado'] ?? '-') }}</td>
                                    <td>{{ $item['observacion'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Sin registros</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection
