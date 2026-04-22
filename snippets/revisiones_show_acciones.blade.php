{{-- REEMPLAZA SOLO EL BLOQUE DE ACCIONES DE LA TABLA DOCUMENTOS ASOCIADOS POR ESTE --}}

<td class="text-center">
    <div class="d-inline-flex gap-2">
        <a href="{{ route('contractual.revisiones.documentos.show', [$revision, $documento]) }}"
           target="_blank"
           class="btn btn-sm btn-outline-primary"
           title="{{ $isPdf ? 'Ver documento' : 'Abrir / descargar documento' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </a>

        <a href="{{ route('contractual.revisiones.documentos.download', [$revision, $documento]) }}"
           class="btn btn-sm btn-outline-secondary"
           title="Descargar documento">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
        </a>

        <form action="{{ route('contractual.revisiones.documentos.reprocess', [$revision, $documento]) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('¿Deseas reprocesar este documento?');">
            @csrf
            <button type="submit"
                    class="btn btn-sm btn-outline-warning"
                    title="Reprocesar documento">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.13-3.36L23 10"></path>
                    <path d="M20.49 15a9 9 0 0 1-14.13 3.36L1 14"></path>
                </svg>
            </button>
        </form>

        <form action="{{ route('contractual.revisiones.documentos.destroy', [$revision, $documento]) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('¿Está seguro de eliminar este documento?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="btn btn-sm btn-outline-danger"
                    title="Eliminar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6l-1 14H6L5 6"></path>
                    <path d="M10 11v6"></path>
                    <path d="M14 11v6"></path>
                    <path d="M9 6V4h6v2"></path>
                </svg>
            </button>
        </form>
    </div>
</td>
