@extends('layouts.theme.app')

@section('content')
<div class="container-fluid">

    @php
        // SEMÁFORO
        $riesgo = 'bajo';
        if ($snapshot->hallazgos->contains('nivel_criticidad', 'alta')) {
            $riesgo = 'alto';
        } elseif ($snapshot->hallazgos->contains('nivel_criticidad', 'media')) {
            $riesgo = 'medio';
        }

        // CONTADORES
        $stats = [
            'cumple' => $snapshot->checklist->where('estado_item', 'cumple')->count(),
            'no_cumple' => $snapshot->checklist->where('estado_item', 'no_cumple')->count(),
            'pendiente' => $snapshot->checklist->where('estado_item', 'pendiente_verificar')->count(),
            'no_se_encuentra' => $snapshot->checklist->where('estado_item', 'no_se_encuentra')->count(),
        ];
    @endphp

    {{-- SEMÁFORO --}}
    <div class="card mb-4 shadow-sm text-center">
        <div class="card-body">
            <h5>Riesgo General</h5>
            <span class="badge 
                @if($riesgo == 'alto') bg-danger
                @elseif($riesgo == 'medio') bg-warning text-dark
                @else bg-success
                @endif
                p-3 fs-5">
                {{ strtoupper($riesgo) }}
            </span>
        </div>
    </div>

    {{-- CONTADORES --}}
    <div class="row mb-4 text-center">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Cumple</h6>
                    <h4>{{ $stats['cumple'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>No Cumple</h6>
                    <h4>{{ $stats['no_cumple'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6>Pendiente</h6>
                    <h4>{{ $stats['pendiente'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6>No se encuentra</h6>
                    <h4>{{ $stats['no_se_encuentra'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- CHECKLIST --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Checklist
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($snapshot->checklist as $c)
                    <tr class="
                        @if($c->estado_item == 'no_cumple') table-danger
                        @elseif($c->estado_item == 'pendiente_verificar') table-warning
                        @elseif($c->estado_item == 'no_se_encuentra') table-secondary
                        @endif
                    ">
                        <td>{{ $c->orden }}</td>
                        <td>{{ $c->item }}</td>
                        <td>{{ $c->estado_item }}</td>
                        <td>{{ $c->observacion }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
