@extends('layouts.theme.app')

@section('title', 'Documentos Asociados')
@section('title2', 'Documentos')

@section('content')
@php
    $docs = $revision->documentos->sortByDesc('id');
@endphp

<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Documentos Asociados</h4>
            <div class="text-muted">Revisión #{{ $revision->id }} · {{ $revision->titulo }}</div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('contractual.revisiones.show', $revision) }}" class="btn btn-outline-secondary btn-sm">
                Volver
            </a>
        </div>
    </div>

    <div class="widget widget-table-one">
        <div class="widget-heading d-flex justify-content-between align-items-center">
            <h5 class="">Listado completo</h5>

            <div class="d-flex flex-wrap gap-2">
                <select id="filterType" class="form-control form-control-sm" style="min-width: 180px;">
                    <option value="">Todos los tipos</option>
                    <option value="pdf">PDF</option>
                    <option value="docx">DOCX</option>
                </select>

                <select id="filterSource" class="form-control form-control-sm" style="min-width: 180px;">
                    <option value="">Todas las fuentes</option>
                    <option value="texto">Texto</option>
                    <option value="ocr">OCR</option>
                    <option value="word">Word</option>
                </select>

                <select id="filterStatus" class="form-control form-control-sm" style="min-width: 220px;">
                    <option value="">Todos los estados</option>
                    <option value="EXTRAIDO">EXTRAIDO</option>
                    <option value="EXTRAIDO_OCR">EXTRAIDO_OCR</option>
                    <option value="SIN_TEXTO">SIN_TEXTO</option>
                    <option value="ERROR_EXTRACCION">ERROR_EXTRACCION</option>
                </select>
            </div>
        </div>

        <div class="widget-content">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0" id="tablaDocumentos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre original</th>
                            <th>Tipo</th>
                            <th>Tamaño</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($docs as $documento)
                            @php
                                $ext = strtolower($documento->extension ?? '');
                                $estado = strtoupper($documento->extraccion_estado ?? '-');
                            @endphp
                            <tr data-type="{{ $ext }}" data-source="{{ strtolower($documento->fuente_texto ?? '') }}" data-status="{{ $estado }}">
                                <td>{{ $documento->id }}</td>
                                <td>{{ $documento->nombre_original }}</td>
                                <td>
                                    {{ $documento->tipo_documento ?: strtoupper($documento->extension ?? '-') }}
                                    <div class="small text-muted">{{ strtoupper($documento->fuente_texto ?? '-') }}</div>
                                </td>
                                <td>
                                    @if(!is_null($documento->peso_bytes))
                                        {{ number_format($documento->peso_bytes / 1024, 1, ',', '.') }} KB
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $cls = 'badge-light border';
                                        if ($documento->extraccion_estado === 'EXTRAIDO') $cls = 'badge-success';
                                        elseif ($documento->extraccion_estado === 'EXTRAIDO_OCR') $cls = 'badge-warning';
                                        elseif ($documento->extraccion_estado === 'SIN_TEXTO') $cls = 'badge-secondary';
                                        elseif ($documento->extraccion_estado === 'ERROR_EXTRACCION') $cls = 'badge-danger';
                                    @endphp
                                    <span class="badge {{ $cls }}">{{ $estado }}</span>
                                </td>
                                <td>{{ $documento->usuario->name ?? 'N/D' }}</td>
                                <td>{{ optional($documento->created_at)->format('d-m-Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('contractual.revisiones.documentos.show', [$revision, $documento]) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            Ver
                                        </a>
                                        <a href="{{ route('contractual.revisiones.documentos.download', [$revision, $documento]) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            Descargar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No existen documentos asociados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const type = document.getElementById('filterType');
    const source = document.getElementById('filterSource');
    const status = document.getElementById('filterStatus');
    const rows = Array.from(document.querySelectorAll('#tablaDocumentos tbody tr'));

    function applyFilters() {
        rows.forEach(row => {
            const okType = !type.value || row.dataset.type === type.value;
            const okSource = !source.value || row.dataset.source === source.value;
            const okStatus = !status.value || row.dataset.status === status.value;
            row.style.display = (okType && okSource && okStatus) ? '' : 'none';
        });
    }

    [type, source, status].forEach(el => el && el.addEventListener('change', applyFilters));
})();
</script>
@endsection
