{{-- BLOQUE RESUMEN DOCUMENTOS CORREGIDO --}}

<div class="row mb-3">
    <div class="col-md-12">

        <div class="mb-2">
            <strong>Total documentos:</strong> {{ $stats['total'] }}
        </div>

        <div class="mb-1">
            <small class="text-muted">Formato</small><br>
            <span class="badge badge-light border">PDF: {{ $stats['pdf'] }}</span>
            <span class="badge badge-light border">DOCX: {{ $stats['docx'] }}</span>
        </div>

        <div class="mb-1">
            <small class="text-muted">Fuente de texto</small><br>
            <span class="badge badge-light border">Texto: {{ $stats['texto'] }}</span>
            <span class="badge badge-light border">OCR: {{ $stats['ocr'] }}</span>
            <span class="badge badge-light border">Word: {{ $stats['word'] }}</span>
        </div>

        <div>
            <small class="text-muted">Estado</small><br>
            <span class="badge badge-light border">Sin texto: {{ $stats['sin_texto'] }}</span>
            <span class="badge badge-light border">Error: {{ $stats['error'] }}</span>
        </div>

    </div>
</div>
