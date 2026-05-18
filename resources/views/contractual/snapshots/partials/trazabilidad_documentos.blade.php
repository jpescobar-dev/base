<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>Documentos utilizados en este snapshot</strong>
    </div>
    <div class="card-body">
        @php
            $items = $snapshot->documentosTraza ?? collect();
        @endphp

        @forelse($items as $item)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                    <div>
                        <strong>{{ $item->documento->nombre_original ?? 'Documento' }}</strong>
                    </div>
                    <div class="small text-muted">
                        {{ optional($item->created_at)->format('d-m-Y H:i') }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div><strong>Fuente usada:</strong> {{ strtoupper($item->fuente_texto_usada ?? '-') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Estado extracción:</strong> {{ strtoupper($item->estado_extraccion ?? '-') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Usuario:</strong> {{ $item->usuario->name ?? 'N/D' }}</div>
                    </div>
                </div>

                @if($item->documento)
                    <div class="mt-2">
                        <a href="{{ route('contractual.revisiones.documentos.show', [$snapshot->revision_contractual_id, $item->documento]) }}"
                           class="btn btn-outline-secondary btn-sm"
                           target="_blank">
                            Ver documento
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="alert alert-light border mb-0">
                No hay documentos trazados para este snapshot.
            </div>
        @endforelse
    </div>
</div>
