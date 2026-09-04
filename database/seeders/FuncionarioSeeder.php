<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FuncionarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::where('role_name', 'Funcionario')->orderBy('id')->get();

        $dependencias = [
            'Secretaría de Ambiente',
            'Administración Zonal Centro (AZCA)',
            'Secretaría de Movilidad',
            'Comisaría Zonal Sur',
        ];

        foreach ($usuarios as $index => $user) {
            $existeFuncionario = DB::table('funcionarios')->where('user_id', $user->id)->exists();

            if ($existeFuncionario) {
                continue; // Salta a la siguiente iteración si ya existe un funcionario para este usuario
            }
            $nomination = DB::table('nominations')
                ->where('candidate_user_id', $user->id)
                ->where('role_name', 'Funcionario')
                ->first();

            DB::table('funcionarios')->insert([
                'user_role'           => 'Funcionario',
                'user_id'             => $user->id,
                'nomination_id'       => $nomination->id,
                'email'               => $user->email,
                'password'            => Hash::make('password123'),
                'phone'               => $user->phone,
                'timezone'            => 'America/Guayaquil',
                'language'            => 'es',
                'is_active'           => true,
                'dependencia_dmq'     => $dependencias[$index % count($dependencias)],
                'calle_principal'     => 'Av. 6 de Diciembre',
                'numero'              => 'F' . (400 + $index),
                'calle_secundaria'    => 'Calle Administrativa ' . ($index + 1),
                'referencias'         => 'Edificio administrativo DMQ',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}
