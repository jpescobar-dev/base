@extends('layouts.theme.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Comparador de Snapshots</h3>
            <p class="text-muted mb-0">
                Revisión #{{ $revision->id }} | Comparando versión {{ $snapshot1->numero_version }} vs {{ $snapshot2->numero_version }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('contractual.revisiones.show', $revision) }}" class="btn btn-outline-secondary">
                Volver a revisión
            </a>

            <a href="{{ route('contractual.revisiones.snapshots.compare.export-word', [$revision, $snapshot1, $snapshot2]) }}"
               class="btn btn-outline-primary">
                Exportar comparación Word
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
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
                            onchange="if(this.value && this.form?.querySelector('[name=snapshot1_id]')?.value){ window.location='{{ route('contractual.revisiones.snapshots.compare', [$revision, '__S1__', '__S2__']) }}'.replace('__S1__', this.form.querySelector('[name=snapshot1_id]').value).replace('__S2__', this.value);}">
                        @foreach($snapshots as $s)
                            <option value="{{ $s->id }}" {{ $s->id == $snapshot2->id ? 'selected' : '' }}>
                                Versión {{ $s->numero_version }} {{ $s->es_actual ? '(actual)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot2]) }}" class="btn btn-outline-dark w-100">
                        Ver snapshot
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 text-center">
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body">
                    <div class="small">Hallazgos nuevos</div>
                    <div class="fs-4 fw-bold">{{ $resumen['hallazgos_nuevos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 bg-danger text-white">
                <div class="card-body">
                    <div class="small">Hallazgos eliminados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['hallazgos_eliminados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="small">Hallazgos modificados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['hallazgos_modificados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 bg-info text-dark">
                <div class="card-body">
                    <div class="small">Checklist nuevos</div>
                    <div class="fs-4 fw-bold">{{ $resumen['checklist_nuevos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="small">Ítems mejorados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['checklist_mejorados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 h-100 bg-secondary text-white">
                <div class="card-body">
                    <div class="small">Ítems empeorados</div>
                    <div class="fs-4 fw-bold">{{ $resumen['checklist_empeorados'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white"><strong>Snapshot base</strong></div>
                <div class="card-body">
                    <p class="mb-2"><strong>Versión:</strong> {{ $snapshot1->numero_version }}</p>
                    <p class="mb-2"><strong>Hallazgos:</strong> {{ $snapshot1->hallazgos->count() }}</p>
                    <p class="mb-0"><strong>Checklist:</strong> {{ $snapshot1->checklist->count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white"><strong>Snapshot comparado</strong></div>
                <div class="card-body">
                    <p class="mb-2"><strong>Versión:</strong> {{ $snapshot2->numero_version }}</p>
                    <p class="mb-2"><strong>Hallazgos:</strong> {{ $snapshot2->hallazgos->count() }}</p>
                    <p class="mb-0"><strong>Checklist:</strong> {{ $snapshot2->checklist->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    @include('contractual.snapshots.partials.compare_hallazgos', [
        'titulo' => 'Hallazgos nuevos',
        'items' => $hallazgosNuevos,
        'color' => 'success',
    ])

    @include('contractual.snapshots.partials.compare_hallazgos', [
        'titulo' => 'Hallazgos eliminados',
        'items' => $hallazgosEliminados,
        'color' => 'danger',
    ])

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <strong>Hallazgos modificados</strong>
        </div>
        <div class="card-body">
            @forelse($hallazgosModificados as $item)
                <div class="border rounded p-3 mb-3">
                    <div class="fw-bold mb-3">{{ $item['titulo'] }}</div>

                    @foreach($item['cambios'] as $cambio)
                        <div class="mb-3">
                            <span class="badge bg-dark mb-2">{{ $cambio['campo'] }}</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="small text-muted">Antes</div>
                                        <div>{{ $cambio['antes'] ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="small text-muted">Después</div>
                                        <div>{{ $cambio['despues'] ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            <td>{{ $c['observacion_despues'] ?: '-' }}</td>
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

    <div class="row g-3">
        <div class="col-lg-6">
            @include('contractual.snapshots.partials.compare_checklist_simple', [
                'titulo' => 'Checklist nuevos',
                'items' => $checklistNuevos,
                'color' => 'info'
            ])
        </div>

        <div class="col-lg-6">
            @include('contractual.snapshots.partials.compare_checklist_simple', [
                'titulo' => 'Checklist eliminados',
                'items' => $checklistEliminados,
                'color' => 'secondary'
            ])
        </div>
    </div>

</div>
@endsection
