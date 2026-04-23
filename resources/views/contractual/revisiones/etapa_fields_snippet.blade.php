<div class="col-md-6 mb-3">
    <label for="etapa_proceso_contractual" class="form-label">Etapa del proceso contractual</label>
    <select name="etapa_proceso_contractual" id="etapa_proceso_contractual" class="form-control" required>
        <option value="pre_adjudicacion" {{ old('etapa_proceso_contractual', $revision->etapa_proceso_contractual ?? 'pre_adjudicacion') === 'pre_adjudicacion' ? 'selected' : '' }}>Pre adjudicación</option>
        <option value="adjudicado_sin_contrato" {{ old('etapa_proceso_contractual', $revision->etapa_proceso_contractual ?? '') === 'adjudicado_sin_contrato' ? 'selected' : '' }}>Adjudicado sin contrato</option>
        <option value="contratado" {{ old('etapa_proceso_contractual', $revision->etapa_proceso_contractual ?? '') === 'contratado' ? 'selected' : '' }}>Contratado</option>
        <option value="en_ejecucion" {{ old('etapa_proceso_contractual', $revision->etapa_proceso_contractual ?? '') === 'en_ejecucion' ? 'selected' : '' }}>En ejecución</option>
    </select>
</div>
