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

        $existencia = $snapshot->checklist->where('tipo_checklist', 'existencia')->sortBy('orden');
        $coherencia = $snapshot->checklist->where('tipo_checklist', 'coherencia')->sortBy('orden');
        $cumplimiento = $snapshot->checklist->where('tipo_checklist', 'cumplimiento')->sortBy('orden');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Snapshot Revisión Contractual</h3>
            <p class="text-muted mb-0">Versión #{{ $snapshot->numero_version }}</p>
        </div>
        <a href="{{ route('contractual.revisiones.show', $snapshot->revision_contractual_id) }}" class="btn btn-outline-secondary">
            Volver a revisión
        </a>
    </div>

    <div class="card mb-4 shadow-sm text-center">
        <div class="card-body">
            <h5 class="mb-3">Riesgo General</h5>
            <span class="badge
                @if($riesgo == 'alto') bg-danger
                @elseif($riesgo == 'medio') bg-warning text-dark
                @else bg-success
                @endif
                p-3 fs-5">
                {{ strtoupper($riesgo) }}
            </span>
        </div>
    </div>

    <div class="row mb-4 text-center">
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Cumple</h6>
                    <h4 class="mb-0">{{ $stats['cumple'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">No Cumple</h6>
                    <h4 class="mb-0">{{ $stats['no_cumple'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Pendiente</h6>
                    <h4 class="mb-0">{{ $stats['pendiente'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-secondary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">No se encuentra</h6>
                    <h4 class="mb-0">{{ $stats['no_se_encuentra'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Resumen</strong>
        </div>
        <div class="card-body">
            <p><strong>Tipo Contrato:</strong> {{ $resumen['tipo_contrato'] ?? '-' }}</p>
            <p><strong>Entidad:</strong> {{ $resumen['entidad'] ?? '-' }}</p>
            <p>
                <strong>Riesgo General:</strong>
                <span class="badge
                    @if(($resumen['riesgo_general'] ?? '') == 'alto') bg-danger
                    @elseif(($resumen['riesgo_general'] ?? '') == 'medio') bg-warning text-dark
                    @else bg-success
                    @endif">
                    {{ strtoupper($resumen['riesgo_general'] ?? '-') }}
                </span>
            </p>

            <p class="mb-2"><strong>Observaciones Clave:</strong></p>
            <ul class="mb-0">
                @foreach(($resumen['observaciones_clave'] ?? []) as $obs)
                    <li>{{ $obs }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold">Hallazgos</h5>
        </div>

        @forelse($snapshot->hallazgos as $h)
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold">{{ $h->titulo }}</h6>

                        <p class="mb-2">
                            <span class="badge
                                @if($h->nivel_criticidad == 'alta') bg-danger
                                @elseif($h->nivel_criticidad == 'media') bg-warning text-dark
                                @else bg-success
                                @endif">
                                {{ strtoupper($h->nivel_criticidad) }}
                            </span>
                            <span class="badge bg-secondary">{{ $h->tipo_riesgo }}</span>
                        </p>

                        <p><strong>Descripción:</strong><br>{{ $h->observacion }}</p>
                        <p class="mb-0"><strong>Recomendación:</strong><br>{{ $h->recomendacion }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border">No existen hallazgos para este snapshot.</div>
            </div>
        @endforelse
    </div>

    <div class="mb-4">
        <h5 class="fw-bold">Checklist por capas</h5>
    </div>

    @include('contractual.snapshots.partials.checklist_table', [
        'titulo' => 'Existencia documental',
        'items' => $existencia,
    ])

    @include('contractual.snapshots.partials.checklist_table', [
        'titulo' => 'Coherencia documental',
        'items' => $coherencia,
    ])

    @include('contractual.snapshots.partials.checklist_table', [
        'titulo' => 'Cumplimiento',
        'items' => $cumplimiento,
    ])

</div>
@endsection
