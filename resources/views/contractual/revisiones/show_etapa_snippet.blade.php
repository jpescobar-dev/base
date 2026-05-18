<tr>
    <th>Etapa del proceso</th>
    <td>{{ strtoupper(str_replace('_', ' ', $revision->etapa_proceso_contractual ?? 'pre_adjudicacion')) }}</td>
    <th>Total documentos</th>
    <td>{{ $revision->documentos->count() }}</td>
</tr>
