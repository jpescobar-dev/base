<div class="card shadow-sm mb-3">
    <div class="card-header {{ $gate['estado'] === 'apto' ? 'bg-success text-white' : 'bg-danger text-white' }}">
        <strong>Estado del análisis</strong>
    </div>
    <div class="card-body">

        <p><strong>Total obligatorios:</strong> {{ $gate['total_obligatorios'] }}</p>
        <p><strong>Críticos presentes:</strong> {{ $gate['criticos_presentes'] }}</p>

        <div class="mb-2">
            <strong>Presentes:</strong>
            <ul>
                @foreach($gate['presentes'] as $p)
                    <li>{{ $p }}</li>
                @endforeach
            </ul>
        </div>

        @if(count($gate['faltantes']) > 0)
            <div class="mb-2 text-danger">
                <strong>Faltantes:</strong>
                <ul>
                    @foreach($gate['faltantes'] as $f)
                        <li>{{ $f }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($gate['estado'] !== 'apto')
            <div class="alert alert-danger">
                <strong>No apto para análisis</strong><br>
                @foreach($gate['mensajes'] as $m)
                    - {{ $m }}<br>
                @endforeach
            </div>
        @else
            <div class="alert alert-success">
                <strong>Apto para análisis</strong>
            </div>
        @endif

    </div>
</div>
