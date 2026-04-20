<div class="card shadow-sm mb-4">
    <div class="card-header bg-{{ $color }} {{ in_array($color, ['warning','info']) ? 'text-dark' : 'text-white' }}">
        <strong>{{ $titulo }}</strong>
    </div>
    <div class="card-body">
        @forelse($items as $h)
            <div class="border rounded p-3 mb-3">
                <h6 class="fw-bold mb-2">{{ $h->titulo }}</h6>
                <p class="mb-2">
                    <span class="badge bg-secondary">{{ $h->tipo_riesgo }}</span>
                    <span class="badge 
                        @if($h->nivel_criticidad == 'alta') bg-danger
                        @elseif($h->nivel_criticidad == 'media') bg-warning text-dark
                        @else bg-success
                        @endif">
                        {{ strtoupper($h->nivel_criticidad) }}
                    </span>
                </p>
                <p class="mb-1"><strong>Descripción:</strong> {{ $h->observacion }}</p>
                <p class="mb-0"><strong>Recomendación:</strong> {{ $h->recomendacion }}</p>
            </div>
        @empty
            <div class="text-muted">No hay registros en esta sección.</div>
        @endforelse
    </div>
</div>
