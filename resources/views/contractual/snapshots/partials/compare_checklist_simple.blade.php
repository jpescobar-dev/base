<div class="card shadow-sm mb-4">
    <div class="card-header bg-{{ $color }} {{ in_array($color, ['warning','info']) ? 'text-dark' : 'text-white' }}">
        <strong>{{ $titulo }}</strong>
    </div>
    <div class="card-body">
        <ul class="mb-0">
            @forelse($items as $item)
                <li class="mb-2">
                    <strong>{{ ucfirst($item->tipo_checklist) }}:</strong>
                    {{ $item->item }}
                    <br>
                    <small class="text-muted">{{ $item->observacion }}</small>
                </li>
            @empty
                <li class="text-muted">No hay registros en esta sección.</li>
            @endforelse
        </ul>
    </div>
</div>
