@php
    $cierre = $cierreRevision ?? ['estado' => 'observada', 'motivos' => [], 'resumen' => ''];
    $badge = match($cierre['estado']) {
        'apta' => 'bg-success',
        'observada' => 'bg-warning text-dark',
        default => 'bg-danger',
    };
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>Evaluación de cierre de revisión</strong>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <span class="badge {{ $badge }}" style="font-size: 0.95rem;">
                {{ strtoupper(str_replace('_', ' ', $cierre['estado'] ?? 'observada')) }}
            </span>
        </div>

        <p><strong>Conclusión:</strong> {{ $cierre['resumen'] ?? '-' }}</p>

        @if(!empty($cierre['motivos']))
            <div class="mt-3">
                <strong>Motivos:</strong>
                <ul class="mb-0">
                    @foreach($cierre['motivos'] as $m)
                        <li>{{ $m }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($cierre['metricas']))
            <div class="row mt-3">
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Hallazgos altos</div>
                        <div class="h4 mb-0">{{ $cierre['metricas']['hallazgos_altos'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Contradicciones altas</div>
                        <div class="h4 mb-0">{{ $cierre['metricas']['contradicciones_altas'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Hallazgos agravados</div>
                        <div class="h4 mb-0">{{ $cierre['metricas']['agravados_hallazgos'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Contradicciones agravadas</div>
                        <div class="h4 mb-0">{{ $cierre['metricas']['agravadas_contradicciones'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
