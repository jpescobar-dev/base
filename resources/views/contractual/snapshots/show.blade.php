@extends('layouts.theme.app')

@section('content')
<div class="container-fluid">

    @php
        $riesgo = 'bajo';
        if ($snapshot->hallazgos->contains('nivel_criticidad', 'alta')) {
            $riesgo = 'alto';
        } elseif ($snapshot->hallazgos->contains('nivel_criticidad', 'media')) {
            $riesgo = 'medio';
        }

        $stats = [
            'cumple' => $snapshot->checklist->where('estado_item', 'cumple')->count(),
            'no_cumple' => $snapshot->checklist->where('estado_item', 'no_cumple')->count(),
            'pendiente' => $snapshot->checklist->where('estado_item', 'pendiente_verificar')->count(),
            'no_se_encuentra' => $snapshot->checklist->where('estado_item', 'no_se_encuentra')->count(),
        ];

        $resumen = $snapshot->json_resultado['resumen'] ?? [];
        $contradicciones = $snapshot->json_resultado['contradicciones_documentales'] ?? [];

        $existencia = $snapshot->checklist->where('tipo_checklist', 'existencia')->sortBy('orden');
        $coherencia = $snapshot->checklist->where('tipo_checklist', 'coherencia')->sortBy('orden');
        $cumplimiento = $snapshot->checklist->where('tipo_checklist', 'cumplimiento')->sortBy('orden');

        $snapshots = $snapshot->revision->snapshots()->orderBy('numero_version', 'desc')->get();
        $snapshotAnterior = $snapshots->where('numero_version', '<', $snapshot->numero_version)->sortByDesc('numero_version')->first();
        $snapshotSiguiente = $snapshots->where('numero_version', '>', $snapshot->numero_version)->sortBy('numero_version')->first();
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Snapshot Revisión Contractual</h3>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted">Versión #{{ $snapshot->numero_version }}</span>

                @if($snapshot->es_actual)
                    <span class="badge bg-success">Versión actual</span>
                @else
                    <span class="badge bg-secondary">Versión histórica</span>
                @endif

                <span class="badge 
                    @if($riesgo == 'alto') bg-danger
                    @elseif($riesgo == 'medio') bg-warning text-dark
                    @else bg-success
                    @endif">
                    Riesgo {{ strtoupper($riesgo) }}
                </span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('contractual.revisiones.show', $snapshot->revision_contractual_id) }}" class="btn btn-outline-secondary">
                Volver a revisión
            </a>

            <a href="{{ route('contractual.revisiones.snapshots.export-word', [$snapshot->revision_contractual_id, $snapshot]) }}"
               class="btn btn-outline-primary">
                Exportar Word
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Ir a versión</label>
                    <select class="form-select" onchange="if(this.value){window.location=this.value}">
                        @foreach($snapshots as $s)
                            <option value="{{ route('contractual.revisiones.snapshots.show', [$snapshot->revision_contractual_id, $s]) }}"
                                {{ $s->id === $snapshot->id ? 'selected' : '' }}>
                                Versión {{ $s->numero_version }} {{ $s->es_actual ? '(actual)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">
                        @if($snapshotAnterior)
                            <a href="{{ route('contractual.revisiones.snapshots.show', [$snapshot->revision_contractual_id, $snapshotAnterior]) }}"
                               class="btn btn-outline-dark">
                                ← Versión anterior
                            </a>
                        @endif

                        @if($snapshotSiguiente)
                            <a href="{{ route('contractual.revisiones.snapshots.show', [$snapshot->revision_contractual_id, $snapshotSiguiente]) }}"
                               class="btn btn-outline-dark">
                                Versión siguiente →
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body">
                    <div class="small text-uppercase">Cumple</div>
                    <div class="fs-3 fw-bold">{{ $stats['cumple'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-danger text-white">
                <div class="card-body">
                    <div class="small text-uppercase">No cumple</div>
                    <div class="fs-3 fw-bold">{{ $stats['no_cumple'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="small text-uppercase">Pendiente</div>
                    <div class="fs-3 fw-bold">{{ $stats['pendiente'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-secondary text-white">
                <div class="card-body">
                    <div class="small text-uppercase">No se encuentra</div>
                    <div class="fs-3 fw-bold">{{ $stats['no_se_encuentra'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <strong>Resumen ejecutivo</strong>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small mb-1">Tipo de contrato</div>
                        <div class="fw-semibold">{{ $resumen['tipo_contrato'] ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small mb-1">Entidad</div>
                        <div class="fw-semibold">{{ $resumen['entidad'] ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3 h-100 text-center">
                        <div class="text-muted small mb-1">Riesgo</div>
                        <span class="badge 
                            @if(($resumen['riesgo_general'] ?? '') == 'alto') bg-danger
                            @elseif(($resumen['riesgo_general'] ?? '') == 'medio') bg-warning text-dark
                            @else bg-success
                            @endif">
                            {{ strtoupper($resumen['riesgo_general'] ?? '-') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="border rounded p-3">
                <div class="fw-semibold mb-2">Observaciones clave</div>
                <ul class="mb-0">
                    @forelse(($resumen['observaciones_clave'] ?? []) as $obs)
                        <li>{{ $obs }}</li>
                    @empty
                        <li class="text-muted">No existen observaciones clave registradas.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <strong>Contradicciones documentales detectadas</strong>
        </div>
        <div class="card-body">
            @forelse($contradicciones as $c)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <h6 class="fw-bold mb-0">{{ $c['etiqueta'] ?? 'Campo sin etiqueta' }}</h6>
                        <span class="badge {{ ($c['criticidad'] ?? '') === 'alta' ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ strtoupper($c['criticidad'] ?? 'media') }}
                        </span>
                    </div>
                    <p class="mb-2">{{ $c['descripcion'] ?? '-' }}</p>
                    <div class="small text-muted mb-2">Valores detectados</div>
                    <ul class="mb-3">
                        @foreach(($c['valores'] ?? []) as $v)
                            <li><strong>{{ $v['documento'] ?? 'Documento' }}:</strong> {{ $v['valor'] ?? '-' }}</li>
                        @endforeach
                    </ul>
                    <p class="mb-0"><strong>Recomendación:</strong> {{ $c['recomendacion'] ?? '-' }}</p>
                </div>
            @empty
                <div class="alert alert-light border mb-0">
                    No se detectaron contradicciones documentales heurísticas en este snapshot.
                </div>
            @endforelse
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h5 class="fw-bold mb-0">Hallazgos</h5>
        <div class="small text-muted">Total: {{ $snapshot->hallazgos->count() }}</div>
    </div>

    <div class="row g-3 mb-4">
        @forelse($snapshot->hallazgos as $h)
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                            <h6 class="fw-bold mb-0">{{ $h->titulo ?: 'Hallazgo' }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge 
                                    @if($h->nivel_criticidad == 'alta') bg-danger
                                    @elseif($h->nivel_criticidad == 'media') bg-warning text-dark
                                    @else bg-success
                                    @endif">
                                    {{ strtoupper($h->nivel_criticidad ?? '-') }}
                                </span>

                                <span class="badge bg-secondary">{{ $h->tipo_riesgo ?: 'sin tipo' }}</span>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="small text-muted mb-1">Descripción</div>
                            <p class="mb-3">{{ $h->observacion ?: '-' }}</p>

                            <div class="small text-muted mb-1">Recomendación</div>
                            <p class="mb-0">{{ $h->recomendacion ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">
                    No existen hallazgos para este snapshot.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Mantén aquí tus includes de checklist por capas existentes --}}
    @include('contractual.snapshots.partials.checklist_table', [
        'titulo' => 'Existencia documental',
        'subtitulo' => 'Verifica si el antecedente o documento está presente en el expediente.',
        'items' => $existencia,
    ])

    @include('contractual.snapshots.partials.checklist_table', [
        'titulo' => 'Coherencia documental',
        'subtitulo' => 'Verifica si los documentos son compatibles entre sí y no presentan contradicciones relevantes.',
        'items' => $coherencia,
    ])

    @include('contractual.snapshots.partials.checklist_table', [
        'titulo' => 'Cumplimiento',
        'subtitulo' => 'Evalúa si las exigencias documentales o contractuales se cumplen con el respaldo disponible.',
        'items' => $cumplimiento,
    ])

</div>
@endsection
