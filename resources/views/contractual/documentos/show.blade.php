@extends('layouts.theme.app')

@section('title', 'Vista Documento')
@section('title2', 'Documento')

@section('content')
<div class="container-fluid">
    <div class="widget-content widget-content-area br-6 mt-2 mb-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
            <div>
                <h4 class="mb-1">Vista Documento</h4>
                <div class="text-muted">{{ $documento->nombre_original }}</div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('contractual.revisiones.show', $revision) }}" class="btn btn-outline-secondary btn-sm">
                    Volver
                </a>
                <a href="{{ route('contractual.revisiones.documentos.download', [$revision, $documento]) }}"
                   class="btn btn-outline-primary btn-sm">
                    Descargar
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3"><strong>Tipo:</strong> {{ $documento->tipo_documento ?: strtoupper($documento->extension ?? '-') }}</div>
            <div class="col-md-3"><strong>Tamaño:</strong> {{ !is_null($documento->peso_bytes) ? number_format($documento->peso_bytes / 1024, 1, ',', '.') . ' KB' : '-' }}</div>
            <div class="col-md-3"><strong>Fuente:</strong> {{ strtoupper($documento->fuente_texto ?? '-') }}</div>
            <div class="col-md-3"><strong>Fecha:</strong> {{ optional($documento->created_at)->format('d-m-Y H:i') }}</div>
        </div>

        @php
            $trazas = $documento->snapshotsTraza ?? collect();
        @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <strong>Trazabilidad documental</strong>
            </div>
            <div class="card-body">
                @forelse($trazas as $traza)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                            <div>
                                <strong>Snapshot v{{ $traza->snapshot->numero_version ?? '-' }}</strong>
                            </div>
                            <div class="small text-muted">
                                {{ optional($traza->created_at)->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div><strong>Fuente usada:</strong> {{ strtoupper($traza->fuente_texto_usada ?? '-') }}</div>
                            </div>
                            <div class="col-md-4">
                                <div><strong>Estado extracción:</strong> {{ strtoupper($traza->estado_extraccion ?? '-') }}</div>
                            </div>
                            <div class="col-md-4">
                                <div><strong>Usuario:</strong> {{ $traza->usuario->name ?? 'N/D' }}</div>
                            </div>
                        </div>

                        @if($traza->snapshot)
                            <div class="mt-2">
                                <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $traza->snapshot]) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    Ver snapshot
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-light border mb-0">
                        Este documento todavía no ha sido vinculado a ningún snapshot de análisis.
                    </div>
                @endforelse
            </div>
        </div>

        @if(strtolower($documento->extension ?? '') === 'pdf')
            <div class="border rounded overflow-hidden" style="height: 80vh;">
                <iframe
                    src="{{ route('contractual.revisiones.documentos.preview', [$revision, $documento]) }}"
                    title="Vista previa PDF"
                    width="100%"
                    height="100%"
                    style="border:0;">
                </iframe>
            </div>
        @else
            <div class="alert alert-light border mb-0">
                Este tipo de archivo no admite vista previa embebida en el navegador.
                Usa el botón <strong>Descargar</strong>.
            </div>
        @endif
    </div>
</div>
@endsection
