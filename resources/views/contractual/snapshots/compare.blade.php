@extends('layouts.theme.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Comparador de Snapshots</h3>
            <p class="text-muted mb-0">
                Revisión #{{ $revision->id }} | Comparando versión {{ $snapshot1->numero_version }} vs {{ $snapshot2->numero_version }}
            </p>
        </div>
        <a href="{{ route('contractual.revisiones.show', $revision) }}" class="btn btn-outline-secondary">
            Volver a revisión
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('contractual.revisiones.snapshots.compare', [$revision, $snapshot1, $snapshot2]) }}">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Snapshot base</label>
                        <select name="snapshot1_id" class="form-select"
                                onchange="if(this.value && document.getElementById('snapshot2_id').value){ window.location='{{ route('contractual.revisiones.snapshots.compare', [$revision, '__S1__', '__S2__']) }}'.replace('__S1__', this.value).replace('__S2__', document.getElementById('snapshot2_id').value);}">
                            @foreach($snapshots as $s)
                                <option value="{{ $s->id }}" {{ $s->id == $snapshot1->id ? 'selected' : '' }}>
                                    Versión {{ $s->numero_version }} {{ $s->es_actual ? '(actual)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Snapshot comparación</label>
                        <select id="snapshot2_id" name="snapshot2_id" class="form-select"
                                onchange="if(this.value && this.form.querySelector('[name=snapshot1_id]').value){ window.location='{{ route('contractual.revisiones.snapshots.compare', [$revision, '__S1__', '__S2__']) }}'.replace('__S1__', this.form.querySelector('[name=snapshot1_id]').value).replace('__S2__', this.value);}">
                            @foreach($snapshots as $s)
                                <option value="{{ $s->id }}" {{ $s->id == $snapshot2->id ? 'selected' : '' }}>
                                    Versión {{ $s->numero_version }} {{ $s->es_actual ? '(actual)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100">
                            <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot1]) }}" class="btn btn-outline-dark w-100">
                                Ver base
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4 text-center">
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <div class="small">Hallazgos nuevos</div>
                    <div class="fs-4 fw-bold">{{ $resumen['hallazgos_nuevos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 bg-danger text-white">
                <div class="card-body">
                    <div class="small">Hallazgos eliminados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['hallazgos_eliminados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                    <div class="small">Hallazgos modificados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['hallazgos_modificados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 bg-info text-dark">
                <div class="card-body">
                    <div class="small">Checklist nuevos</div>
                    <div class="fs-4 fw-bold">{{ $resumen['checklist_nuevos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="small">Ítems mejorados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['checklist_mejorados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 bg-secondary text-white">
                <div class="card-body">
                    <div class="small">Ítems empeorados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['checklist_empeorados'] }}</div>
                </div>
            </div>
        </div>
    </div>

    @include('contractual.snapshots.partials.compare_hallazgos', [
        'titulo' => 'Hallazgos nuevos',
        'items' => $hallazgosNuevos,
        'color' => 'success',
        'modo' => 'simple'
    ])

    @include('contractual.snapshots.partials.compare_hallazgos', [
        'titulo' => 'Hallazgos eliminados',
        'items' => $hallazgosEliminados,
        'color' => 'danger',
        'modo' => 'simple'
    ])

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <strong>Hallazgos modificados</strong>
        </div>
        <div class="card-body">
            @forelse($hallazgosModificados as $item)
                <div class="border rounded p-3 mb-3">
                    <h6 class="fw-bold mb-3">{{ $item['titulo'] }}</h6>
                    @foreach($item['cambios'] as $cambio)
                        <div class="mb-2">
                            <span class="badge bg-dark">{{ $cambio['campo'] }}</span>
                            <div><strong>Antes:</strong> {{ $cambio['antes'] ?: '-' }}</div>
                            <div><strong>Después:</strong> {{ $cambio['despues'] ?: '-' }}</div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            @empty
                <div class="text-muted">No hay hallazgos modificados.</div>
            @endforelse
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <strong>Cambios en checklist</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Capa</th>
                        <th>Ítem</th>
                        <th>Estado anterior</th>
                        <th>Estado nuevo</th>
                        <th>Clasificación</th>
                        <th>Observación nueva</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($checklistCambios as $c)
                        <tr class="
                            @if($c['clasificacion'] === 'mejorado') table-success
                            @elseif($c['clasificacion'] === 'empeorado') table-danger
                            @else table-warning
                            @endif
                        ">
                            <td>{{ ucfirst($c['tipo_checklist']) }}</td>
                            <td>{{ $c['item'] }}</td>
                            <td>{{ $c['estado_antes'] }}</td>
                            <td>{{ $c['estado_despues'] }}</td>
                            <td>{{ ucfirst($c['clasificacion']) }}</td>
                            <td>{{ $c['observacion_despues'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No se detectaron cambios en checklist.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            @include('contractual.snapshots.partials.compare_checklist_simple', [
                'titulo' => 'Checklist nuevos',
                'items' => $checklistNuevos,
                'color' => 'info'
            ])
        </div>
        <div class="col-md-6">
            @include('contractual.snapshots.partials.compare_checklist_simple', [
                'titulo' => 'Checklist eliminados',
                'items' => $checklistEliminados,
                'color' => 'secondary'
            ])
        </div>
    </div>

</div>
@endsection
