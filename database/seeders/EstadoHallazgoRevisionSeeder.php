<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoHallazgoRevisionSeeder extends Seeder
{
    public function run(): void
    {
        $tabla = 'hallazgos_revision';

        $estados = [
            [
                'nombre' => 'NUEVO',
                'descripcion' => 'Hallazgo detectado en la iteración actual',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'PERSISTENTE',
                'descripcion' => 'Hallazgo que persiste respecto de iteraciones previas',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'SUBSANADO',
                'descripcion' => 'Hallazgo corregido o aclarado con nuevo respaldo',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'PENDIENTE_VERIFICACION',
                'descripcion' => 'Hallazgo no resuelto con respaldo documental suficiente',
                'tabla_referencia' => $tabla,
            ],
        ];

        foreach ($estados as $estado) {
            Estado::updateOrCreate(
                [
                    'nombre' => $estado['nombre'],
                    'tabla_referencia' => $estado['tabla_referencia'],
                ],
                [
                    'descripcion' => $estado['descripcion'],
                ]
            );
        }
    }
}