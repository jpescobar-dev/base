<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe de revisión contractual</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #222; }
        h1, h2, h3 { margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #999; padding: 6px; vertical-align: top; }
        .section { margin-bottom: 18px; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>Informe de revisión contractual</h1>
    <p><strong>Revisión:</strong> #{{ $revision->id }} - {{ $revision->titulo }}</p>
    <p><strong>Snapshot:</strong> v{{ $snapshot->numero_version }}</p>

    <div class="section">
        <h2>Estado documental</h2>
        <p><strong>Obligatorios configurados:</strong> {{ $report['gate']['total_obligatorios'] ?? 0 }}</p>
        <p><strong>Críticos presentes:</strong> {{ $report['gate']['criticos_presentes'] ?? 0 }}</p>
        @if(!empty($report['gate']['faltantes']))
            <p><strong>Faltantes:</strong> {{ implode(', ', $report['gate']['faltantes']) }}</p>
        @endif
    </div>

    <div class="section">
        <h2>Resumen ejecutivo</h2>
        <p><strong>Tipo contrato:</strong> {{ $report['resumen']['tipo_contrato'] ?? '-' }}</p>
        <p><strong>Entidad:</strong> {{ $report['resumen']['entidad'] ?? '-' }}</p>
        <p><strong>Riesgo general:</strong> {{ strtoupper($report['resumen']['riesgo_general'] ?? '-') }}</p>
        <ul>
            @foreach(($report['resumen']['observaciones_clave'] ?? []) as $obs)
                <li>{{ $obs }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>Contradicciones con prevalencia</h2>
        @forelse(($report['contradicciones'] ?? []) as $c)
            <p><strong>{{ $c['etiqueta'] ?? '-' }}</strong> ({{ strtoupper($c['criticidad'] ?? '-') }})</p>
            <p>{{ $c['descripcion'] ?? '-' }}</p>
            <p><strong>Documento prevalente:</strong> {{ $c['documento_prevalente'] ?? 'Pendiente de verificar' }}</p>
            <p><strong>Motivo de prevalencia:</strong> {{ $c['motivo_prevalencia'] ?? '-' }}</p>
            <p><strong>Recomendación:</strong> {{ $c['recomendacion'] ?? '-' }}</p>
        @empty
            <p class="muted">No hay contradicciones registradas.</p>
        @endforelse
    </div>

    <div class="section">
        <h2>Hallazgos</h2>
        @forelse(($report['hallazgos'] ?? []) as $h)
            <p><strong>{{ $h['titulo'] ?? '-' }}</strong> ({{ strtoupper($h['criticidad'] ?? '-') }})</p>
            <p>{{ $h['observacion'] ?? '-' }}</p>
            <p><strong>Recomendación:</strong> {{ $h['recomendacion'] ?? '-' }}</p>
        @empty
            <p class="muted">No hay hallazgos registrados.</p>
        @endforelse
    </div>
</body>
</html>
