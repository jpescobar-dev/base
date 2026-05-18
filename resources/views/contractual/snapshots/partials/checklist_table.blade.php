<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <strong>{{ $titulo }}</strong>
                @isset($subtitulo)
                    <div class="small text-white-50">{{ $subtitulo }}</div>
                @endisset
            </div>
            <span class="badge bg-light text-dark">{{ $items->count() }} ítems</span>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">#</th>
                    <th style="width: 28%;">Ítem</th>
                    <th style="width: 180px;">Estado</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $c)
                    <tr class="
                        @if($c->estado_item == 'no_cumple') table-danger
                        @elseif($c->estado_item == 'pendiente_verificar') table-warning
                        @elseif($c->estado_item == 'no_se_encuentra') table-secondary
                        @endif
                    ">
                        <td>{{ $c->orden }}</td>
                        <td>{{ $c->item }}</td>
                        <td>
                            <span class="badge
                                @if($c->estado_item == 'cumple') bg-success
                                @elseif($c->estado_item == 'no_cumple') bg-danger
                                @elseif($c->estado_item == 'pendiente_verificar') bg-warning text-dark
                                @else bg-secondary
                                @endif">
                                {{ strtoupper($c->estado_item) }}
                            </span>
                        </td>
                        <td>{{ $c->observacion ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No existen ítems en esta capa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
