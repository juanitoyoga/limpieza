<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::where('role_name', 'Supervisor')->orderBy('id')->get();

        foreach ($usuarios as $index => $user) {

            $existeSupervisor = DB::table('supervisores')->where('user_id', $user->id)->exists();

            if ($existeSupervisor) {
                continue; // Salta a la siguiente iteración si ya existe un supervisor para este usuario
            }
            $nomination = DB::table('nominations')
                ->where('candidate_user_id', $user->id)
                ->where('role_name', 'Supervisor')
                ->first();

            DB::table('supervisores')->insert([
                'user_id'             => $user->id,
                'nomination_id'       => $nomination->id,
                'email'               => $user->email,
                'role_name'           => 'Supervisor',
                'email_verified_at'   => now(),
                'password'            => Hash::make('password123'),
                'phone'               => $user->phone,
                'timezone'            => 'America/Guayaquil',
                'language'            => 'es',
                'is_active'           => true,
                'dependencia_dmq'     => 'Coordinación Zonal DMQ',
                'calle_principal'     => 'Av. Amazonas',
                'numero'              => 'S' . (300 + $index),
                'calle_secundaria'    => 'Calle Supervisión ' . ($index + 1),
                'referencias'         => 'Oficina zonal DMQ',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}
