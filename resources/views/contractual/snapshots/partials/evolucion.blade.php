@php
    $evHallazgos = $evolucion['hallazgos'] ?? ['nuevos' => [], 'persistentes' => [], 'corregidos' => [], 'agravados' => []];
    $evContradicciones = $evolucion['contradicciones'] ?? ['nuevas' => [], 'persistentes' => [], 'resueltas' => [], 'agravadas' => []];
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>Evolución respecto del snapshot anterior</strong>
    </div>
    <div class="card-body">

        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Hallazgos nuevos</div>
                    <div class="h4 mb-0">{{ count($evHallazgos['nuevos'] ?? []) }}</div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Hallazgos persistentes</div>
                    <div class="h4 mb-0">{{ count($evHallazgos['persistentes'] ?? []) }}</div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Hallazgos corregidos</div>
                    <div class="h4 mb-0">{{ count($evHallazgos['corregidos'] ?? []) }}</div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Hallazgos agravados</div>
                    <div class="h4 mb-0">{{ count($evHallazgos['agravados'] ?? []) }}</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Contradicciones nuevas</div>
                    <div class="h4 mb-0">{{ count($evContradicciones['nuevas'] ?? []) }}</div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Contradicciones persistentes</div>
                    <div class="h4 mb-0">{{ count($evContradicciones['persistentes'] ?? []) }}</div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Contradicciones resueltas</div>
                    <div class="h4 mb-0">{{ count($evContradicciones['resueltas'] ?? []) }}</div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted">Contradicciones agravadas</div>
                    <div class="h4 mb-0">{{ count($evContradicciones['agravadas'] ?? []) }}</div>
                </div>
            </div>
        </div>

        @if(count($evHallazgos['agravados'] ?? []) || count($evContradicciones['agravadas'] ?? []))
            <div class="alert alert-warning mb-0">
                Se detectaron elementos agravados respecto del snapshot anterior. Revisar antes de cerrar el análisis.
            </div>
        @elseif(count($evHallazgos['corregidos'] ?? []) || count($evContradicciones['resueltas'] ?? []))
            <div class="alert alert-success mb-0">
                Existen mejoras respecto del snapshot anterior. Se detectaron correcciones o resoluciones documentales.
            </div>
        @else
            <div class="alert alert-light border mb-0">
                No se detectan cambios relevantes respecto del snapshot anterior, o no existe base previa suficiente.
            </div>
        @endif

    </div>
</div>
