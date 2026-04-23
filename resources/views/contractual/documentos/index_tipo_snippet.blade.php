<td>
    <div>{{ $documento->tipoDocumento->nombre ?? $documento->tipo_documento ?? strtoupper($documento->extension ?? '-') }}</div>
    <div class="small text-muted">
        {{ $documento->tipoDocumento ? strtoupper($documento->tipoDocumento->jerarquia) : strtoupper($documento->fuente_texto ?? '-') }}
    </div>
</td>
