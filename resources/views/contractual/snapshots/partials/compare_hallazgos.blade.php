<div class="card shadow-sm mb-4">
    <div class="card-header bg-{{ $color }} {{ in_array($color, ['warning','info']) ? 'text-dark' : 'text-white' }}">
        <strong>{{ $titulo }}</strong>
    </div>
    <div class="card-body">
        @forelse($items as $h)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                    <div class="fw-bold">{{ $h->titulo ?: 'Hallazgo' }}</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-secondary">{{ $h->tipo_riesgo ?: 'sin tipo' }}</span>
                        <span class="badge
                            @if($h->nivel_criticidad == 'alta') bg-danger
                            @elseif($h->nivel_criticidad == 'media') bg-warning text-dark
                            @else bg-success
                            @endif">
                            {{ strtoupper($h->nivel_criticidad ?? '-') }}
                        </span>
                    </div>
                </div>
                <div class="small text-muted mb-1">Descripción</div>
                <div>{{ $h->observacion ?: '-' }}</div>
            </div>
        @empty
            <div class="text-muted">No hay registros en esta sección.</div>
        @endforelse
    </div>
</div>
