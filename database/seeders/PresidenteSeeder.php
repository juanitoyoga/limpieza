<?php

namespace Database\Seeders;

use App\Models\Barrio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PresidenteSeeder extends Seeder
{
    /**
     * Asigna un Presidente activo a cada barrio existente.
     */
    public function run(): void
    {
        $barrios = Barrio::all();
        $usuarios = User::where('role_name', 'Presidente')->orderBy('id')->get();

        foreach ($barrios as $index => $barrio) {
            $user = $usuarios[$index] ?? null;
            if (!$user) {
                continue;
            }

            $nomination = DB::table('nominations')
                ->where('candidate_user_id', $user->id)
                ->where('role_name', 'Presidente')
                ->first();

            DB::table('presidentes')->insert([
                'barrio_id'           => $barrio->id,
                'user_id'             => $user->id,
                'nomination_id'       => $nomination->id,
                'email'               => $user->email,
                'role_name'           => 'Presidente',
                'email_verified_at'   => now(),
                'password'            => Hash::make('password123'),
                'phone'               => $user->phone,
                'timezone'            => 'America/Guayaquil',
                'language'            => 'es',
                'is_active'           => true,
                'calle_principal'     => 'Av. de los Próceres',
                'numero'              => 'P' . (200 + $index),
                'calle_secundaria'    => 'Pasaje Comunitario ' . ($index + 1),
                'referencias'         => 'Casa comunal del barrio',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}
