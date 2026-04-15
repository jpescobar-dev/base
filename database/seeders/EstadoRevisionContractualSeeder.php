<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoRevisionContractualSeeder extends Seeder
{
    public function run(): void
    {
        $tabla = 'revisiones_contractuales';

        $estados = [
            ['nombre' => 'BORRADOR', 'descripcion' => 'Revisión creada, aún no enviada a análisis', 'tabla_referencia' => $tabla],            
            ['nombre' => 'REVISION', 'descripcion' => 'Revisión en análisis', 'tabla_referencia' => $tabla],
            ['nombre' => 'OBSERVADA', 'descripcion' => 'Revisión con observaciones pendientes', 'tabla_referencia' => $tabla],
            ['nombre' => 'EN_SUBSANACION', 'descripcion' => 'Se están incorporando antecedentes para subsanar observaciones', 'tabla_referencia' => $tabla],
            ['nombre' => 'CONSOLIDADA', 'descripcion' => 'Revisión con snapshot consolidado', 'tabla_referencia' => $tabla],
            ['nombre' => 'CERRADA', 'descripcion' => 'Revisión finalizada y cerrada', 'tabla_referencia' => $tabla],
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