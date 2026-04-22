@extends('layouts.theme.app')

@section('content')
<div class="container-fluid">
    @php
        $resumen = $comparacion['resumen'] ?? ['nuevas' => 0, 'resueltas' => 0, 'persistentes' => 0, 'modificadas' => 0];
    @endphp

    <style>
        .cc-card{border:1px solid #d9dee5;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.04);background:#fff}
        .cc-icon{
            width:28px;height:28px;border-radius:8px;border:1.5px solid currentColor;
            display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;
            background:transparent;line-height:1;
        }
        .cc-stat{border:1px solid #d9dee5;border-radius:14px;background:#fff;padding:16px}
        .cc-muted{color:#6c757d}
        .cc-section-title{display:flex;align-items:center;gap:10px}
        .cc-list-item{border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff}
        .cc-chip{display:inline-block;padding:4px 10px;border:1px solid currentColor;border-radius:999px;background:transparent;font-size:12px}
        .cc-code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:12px}
        .cc-outline-danger{color:#b42318}
        .cc-outline-success{color:#067647}
        .cc-outline-warning{color:#b54708}
        .cc-outline-secondary{color:#475467}
        .cc-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        @media (max-width: 992px){.cc-grid{grid-template-columns:1fr}}
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Comparador de contradicciones</h3>
            <div class="text-muted">
                Revisión #{{ $revision->id }} · versión {{ $snapshot1->numero_version }} vs versión {{ $snapshot2->numero_version }}
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('contractual.revisiones.show', $revision) }}" class="btn btn-outline-secondary">
                Volver a revisión
            </a>
            <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot2]) }}" class="btn btn-outline-dark">
                Ver snapshot actual
            </a>
        </div>
    </div>

    <div class="cc-card p-3 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Snapshot base</label>
                <select class="form-select" id="snapshot1_id">
                    @foreach($snapshots as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $snapshot1->id ? 'selected' : '' }}>
                            Versión {{ $s->numero_version }} {{ $s->es_actual ? '(actual)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Snapshot comparado</label>
                <select class="form-select" id="snapshot2_id">
                    @foreach($snapshots as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $snapshot2->id ? 'selected' : '' }}>
                            Versión {{ $s->numero_version }} {{ $s->es_actual ? '(actual)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100" onclick="goCompare()">
                    Comparar
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="cc-stat h-100">
                <div class="d-flex align-items-center gap-2 mb-2 cc-outline-danger">
                    <span class="cc-icon">+</span>
                    <span class="small text-uppercase">Nuevas</span>
                </div>
                <div class="fs-3 fw-bold">{{ $resumen['nuevas'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cc-stat h-100">
                <div class="d-flex align-items-center gap-2 mb-2 cc-outline-success">
                    <span class="cc-icon">✓</span>
                    <span class="small text-uppercase">Resueltas</span>
                </div>
                <div class="fs-3 fw-bold">{{ $resumen['resueltas'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cc-stat h-100">
                <div class="d-flex align-items-center gap-2 mb-2 cc-outline-warning">
                    <span class="cc-icon">~</span>
                    <span class="small text-uppercase">Modificadas</span>
                </div>
                <div class="fs-3 fw-bold">{{ $resumen['modificadas'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cc-stat h-100">
                <div class="d-flex align-items-center gap-2 mb-2 cc-outline-secondary">
                    <span class="cc-icon">=</span>
                    <span class="small text-uppercase">Persistentes</span>
                </div>
                <div class="fs-3 fw-bold">{{ $resumen['persistentes'] }}</div>
            </div>
        </div>
    </div>

    <div class="cc-card p-4 mb-4">
        <div class="cc-section-title cc-outline-danger mb-3">
            <span class="cc-icon">+</span>
            <h5 class="fw-bold mb-0">Contradicciones nuevas</h5>
        </div>
        <div class="d-grid gap-3">
            @forelse($comparacion['nuevas'] as $item)
                <div class="cc-list-item">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                        <div class="fw-bold">{{ $item->etiqueta }}</div>
                        <span class="cc-chip cc-outline-danger">{{ strtoupper($item->criticidad ?? 'media') }}</span>
                    </div>
                    <p class="mb-2">{{ $item->descripcion ?: '-' }}</p>
                    <div class="small cc-muted mb-1">Valores detectados</div>
                    <ul class="mb-2">
                        @foreach(($item->valores_detectados ?? []) as $v)
                            <li class="cc-code">{{ $v['documento'] ?? 'Documento' }}: {{ $v['valor'] ?? '-' }}</li>
                        @endforeach
                    </ul>
                    <div><strong>Recomendación:</strong> {{ $item->recomendacion ?: '-' }}</div>
                </div>
            @empty
                <div class="alert alert-light border mb-0">No hay contradicciones nuevas.</div>
            @endforelse
        </div>
    </div>

    <div class="cc-card p-4 mb-4">
        <div class="cc-section-title cc-outline-success mb-3">
            <span class="cc-icon">✓</span>
            <h5 class="fw-bold mb-0">Contradicciones resueltas</h5>
        </div>
        <div class="d-grid gap-3">
            @forelse($comparacion['resueltas'] as $item)
                <div class="cc-list-item">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                        <div class="fw-bold">{{ $item->etiqueta }}</div>
                        <span class="cc-chip cc-outline-success">{{ strtoupper($item->criticidad ?? 'media') }}</span>
                    </div>
                    <p class="mb-2">{{ $item->descripcion ?: '-' }}</p>
                    <div><strong>Se resolvió respecto del snapshot base.</strong></div>
                </div>
            @empty
                <div class="alert alert-light border mb-0">No hay contradicciones resueltas.</div>
            @endforelse
        </div>
    </div>

    <div class="cc-card p-4 mb-4">
        <div class="cc-section-title cc-outline-warning mb-3">
            <span class="cc-icon">~</span>
            <h5 class="fw-bold mb-0">Contradicciones modificadas</h5>
        </div>
        <div class="d-grid gap-3">
            @forelse($comparacion['modificadas'] as $pair)
                <div class="cc-list-item">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                        <div class="fw-bold">{{ $pair['ahora']->etiqueta }}</div>
                        <span class="cc-chip cc-outline-warning">{{ strtoupper($pair['ahora']->criticidad ?? 'media') }}</span>
                    </div>
                    <div class="cc-grid">
                        <div class="border rounded p-3">
                            <div class="fw-semibold mb-2">Antes</div>
                            <ul class="mb-0">
                                @foreach(($pair['antes']->valores_detectados ?? []) as $v)
                                    <li class="cc-code">{{ $v['documento'] ?? 'Documento' }}: {{ $v['valor'] ?? '-' }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="border rounded p-3">
                            <div class="fw-semibold mb-2">Ahora</div>
                            <ul class="mb-0">
                                @foreach(($pair['ahora']->valores_detectados ?? []) as $v)
                                    <li class="cc-code">{{ $v['documento'] ?? 'Documento' }}: {{ $v['valor'] ?? '-' }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-light border mb-0">No hay contradicciones modificadas.</div>
            @endforelse
        </div>
    </div>

    <div class="cc-card p-4 mb-4">
        <div class="cc-section-title cc-outline-secondary mb-3">
            <span class="cc-icon">=</span>
            <h5 class="fw-bold mb-0">Contradicciones persistentes</h5>
        </div>
        <div class="d-grid gap-3">
            @forelse($comparacion['persistentes'] as $item)
                <div class="cc-list-item">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                        <div class="fw-bold">{{ $item->etiqueta }}</div>
                        <span class="cc-chip cc-outline-secondary">{{ strtoupper($item->criticidad ?? 'media') }}</span>
                    </div>
                    <p class="mb-2">{{ $item->descripcion ?: '-' }}</p>
                    <div class="small cc-muted">La contradicción persiste sin cambios relevantes respecto del snapshot base.</div>
                </div>
            @empty
                <div class="alert alert-light border mb-0">No hay contradicciones persistentes.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
function goCompare() {
    const s1 = document.getElementById('snapshot1_id').value;
    const s2 = document.getElementById('snapshot2_id').value;
    const url = "{{ route('contractual.revisiones.snapshots.contradicciones.compare', [$revision, '__S1__', '__S2__']) }}"
        .replace('__S1__', s1)
        .replace('__S2__', s2);
    window.location = url;
}
</script>
@endsection
