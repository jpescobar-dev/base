{{-- REEMPLAZA SOLO EL BLOQUE DE ACCIONES DE la tabla completa POR ESTE --}}

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

        <form action="{{ route('contractual.revisiones.documentos.reprocess', [$revision, $documento]) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('¿Deseas reprocesar este documento?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
                Reprocesar
            </button>
        </form>
    </div>
</td>
