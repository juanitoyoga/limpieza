<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ordenanza332;
use App\Models\SalarioMinimo;
use App\Models\PorcentajeMultas;

class PorcentajeMultasSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar la tabla
        PorcentajeMultas::truncate();

        // Salario mínimo vigente
        $salario = SalarioMinimo::vigente();

        if (!$salario) {
            $this->command->error('No existe un salario mínimo registrado.');
            return;
        }

        foreach (Ordenanza332::all() as $ordenanza) {

            PorcentajeMultas::create([
                'ordenanza332_id' => $ordenanza->id,
                'salariominimo_id' => $salario->id,
                'porcentaje' => 20, // ajustar al nombre real del campo
            ]);
        }
    }
}
