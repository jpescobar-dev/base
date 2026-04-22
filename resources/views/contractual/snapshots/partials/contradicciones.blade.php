<div class="card shadow-sm mb-4">
    <div class="card-header bg-danger text-white">
        <strong>Contradicciones documentales detectadas</strong>
    </div>
    <div class="card-body">
        @php
            $items = $snapshot->contradicciones ?? collect();
        @endphp

        @forelse($items as $c)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                    <h6 class="fw-bold mb-0">{{ $c->etiqueta ?: 'Campo sin etiqueta' }}</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge {{ ($c->criticidad ?? '') === 'alta' ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ strtoupper($c->criticidad ?? 'media') }}
                        </span>
                        @if($c->documento_preferente)
                            <span class="badge bg-dark">Preferente: {{ $c->documento_preferente }}</span>
                        @endif
                    </div>
                </div>

                <p class="mb-2">{{ $c->descripcion ?: '-' }}</p>

                <div class="small text-muted mb-2">Valores detectados</div>
                <ul class="mb-3">
                    @foreach(($c->valores_detectados ?? []) as $v)
                        <li>
                            <strong>{{ $v['documento'] ?? 'Documento' }}</strong>
                            @if(!empty($v['tipo_documento']))
                                <span class="text-muted">({{ $v['tipo_documento'] }})</span>
                            @endif
                            : {{ $v['valor'] ?? '-' }}
                        </li>
                    @endforeach
                </ul>

                <p class="mb-0"><strong>Recomendación:</strong> {{ $c->recomendacion ?: '-' }}</p>
            </div>
        @empty
            <div class="alert alert-light border mb-0">
                No se detectaron contradicciones documentales persistidas en este snapshot.
            </div>
        @endforelse
    </div>
</div>
