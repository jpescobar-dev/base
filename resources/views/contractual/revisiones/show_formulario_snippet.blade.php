<div class="col-md-6 mb-3">
    <label for="tipo_documento_contractual_id" class="form-label">Tipo documental del catálogo</label>
    <select name="tipo_documento_contractual_id"
            id="tipo_documento_contractual_id"
            class="form-control">
        <option value="">Seleccionar tipo...</option>
        @foreach(($tiposDocumentales ?? []) as $tipo)
            <option value="{{ $tipo->id }}">
                {{ $tipo->nombre }} ({{ strtoupper($tipo->jerarquia) }})
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label for="tipo_documento" class="form-label">Tipo libre / respaldo</label>
    <input type="text" name="tipo_documento" id="tipo_documento"
           class="form-control @error('tipo_documento') is-invalid @enderror"
           value="{{ old('tipo_documento') }}"
           placeholder="Ej.: Contrato, Bases, Resolución, Garantía">
    @error('tipo_documento')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
