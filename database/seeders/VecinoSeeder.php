<?php

namespace Database\Seeders;

use App\Models\Barrio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VecinoSeeder extends Seeder
{
    /**
     * Crea un registro Vecino por cada usuario con role_name = 'User',
     * repartiéndolos de forma cíclica entre los barrios existentes.
     */
    public function run(): void
    {
        $barrios = Barrio::all();
        $usuarios = User::where('role_name', 'Vecino')->orderBy('id')->get();

        $ocupacionesPool = [
            ['Comerciante'],
            ['Docente'],
            ['Estudiante'],
            ['Empleado público'],
            ['Empleado privado'],
            ['Independiente'],
            ['Ama de casa'],
            ['Jubilado'],
        ];
        $deportesPool = [
            ['Fútbol'],
            ['Natación'],
            ['Ciclismo'],
            ['Atletismo'],
            ['Voleibol'],
            ['Básquet'],
            [],
            ['Ecuavóley'],
        ];
        $recreacionPool = [
            ['Lectura'],
            ['Cine'],
            ['Música'],
            ['Jardinería'],
            ['Manualidades'],
            ['Caminatas'],
            ['Pintura'],
            [],
        ];

        foreach ($usuarios as $index => $user) {
            $existeVecino = DB::table('vecinos')->where('user_id', $user->id)->exists();

            if ($existeVecino) {
                continue; // Salta a la siguiente iteración si ya existe un vecino para este usuario
            }

            $barrio = $barrios[$index % $barrios->count()];

            $cedula = $user->nro_id ?? ('17100' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT));

            \DB::table('vecinos')->insert([
                'id_DMQ'             => $barrio->id_DMQ,
                'user_id'            => $user->id,
                'cedula'             => $cedula,
                'telefono'           => $user->phone,
                'fecha_registro'     => now()->subDays(($index + 1) * 7)->toDateString(),
                'fecha_cancelacion'  => null,
                'ocupacion'          => json_encode($ocupacionesPool[$index % count($ocupacionesPool)]),
                'deportes'           => json_encode($deportesPool[$index % count($deportesPool)]),
                'recreacion'         => json_encode($recreacionPool[$index % count($recreacionPool)]),
                'calle_principal'    => 'Calle ' . chr(65 + ($index % 26)),
                'numero'             => 'OE' . (1000 + $index),
                'calle_secundaria'   => 'Pasaje ' . ($index + 1),
                'referencias'        => 'Casa color ' . ['azul', 'blanca', 'verde', 'amarilla', 'roja'][$index % 5],
                'is_active'          => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}
