<?php

namespace Database\Seeders;

use App\Models\Barrio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DirigenteSeeder extends Seeder
{
    /**
     * Asigna un Dirigente activo a cada barrio existente.
     */
    public function run(): void
    {
        $barrios = Barrio::all();
        $usuarios = User::where('role_name', 'Dirigente')->orderBy('id')->get();

        foreach ($barrios as $index => $barrio) {
            $user = $usuarios[$index] ?? null;
            if (!$user) {
                continue; // No hay suficientes usuarios Dirigente para todos los barrios
            }

            $nomination = DB::table('nominations')
                ->where('candidate_user_id', $user->id)
                ->where('role_name', 'Dirigente')
                ->first();

            DB::table('dirigentes')->insert([
                'barrio_id'           => $barrio->id,
                'user_id'             => $user->id,
                'nomination_id'       => $nomination->id,
                'email'               => $user->email,
                'role_name'           => 'Dirigente',
                'email_verified_at'   => now(),
                'password'            => Hash::make('password123'),
                'phone'               => $user->phone,
                'timezone'            => 'America/Guayaquil',
                'language'            => 'es',
                'is_active'           => true,
                'calle_principal'     => 'Av. Principal',
                'numero'              => 'N' . (100 + $index),
                'calle_secundaria'    => 'Calle Secundaria ' . ($index + 1),
                'referencias'         => 'Cerca del parque central del barrio',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}
