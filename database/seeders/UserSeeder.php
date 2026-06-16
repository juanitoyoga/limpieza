<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barrio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Crea los usuarios base para cada rol.
     * Las cantidades están pensadas para alimentar dashboards iniciales:
     * - 1 Admin
     * - 2 Supervisores
     * - 5 Presidentes (uno por barrio)
     * - 5 Dirigentes (uno por barrio)
     * - 4 Funcionarios (distintas dependencias DMQ)
     * - 25 Vecinos (repartidos entre 5 barrios)
     */
    public function run(): void
    {

        $Barrio = Barrio::activos()
            ->pluck('nombre', 'id_DMQ')
            ->toArray();
        $defaultPassword = Hash::make('password123');

        // ─── Admin ──────────────────────────────────────────
        User::create([
            'tipo_id'    => 'Cedula',
            'nro_id'     => '1700000001',
            'first_name' => 'Admin',
            'last_name'  => 'Sistema',
            'role_name'  => 'Admin',
            'email'      => 'admin@limpiaturincon.ec',
            'password'   => $defaultPassword,
            'phone'      => '0991234567',
            'birthdate'  => '1985-01-15',
            'gender'     => 'M',
            'timezone'   => 'America/Guayaquil',
            'language'   => 'es',
            'is_active'  => true,
            'email_verified_at' => now(),
        ]);

        // ─── Supervisores (2) ───────────────────────────────
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'tipo_id'    => 'Cedula',
                'nro_id'     => '170000001' . $i,
                'first_name' => "Supervisor{$i}",
                'last_name'  => 'DMQ',
                'role_name'  => 'Supervisor',
                'email'      => "supervisor{$i}@limpiaturincon.ec",
                'password'   => $defaultPassword,
                'phone'      => '099123456' . $i,
                'birthdate'  => '1980-0' . $i . '-10',
                'gender'     => $i % 2 == 0 ? 'F' : 'M',
                'timezone'   => 'America/Guayaquil',
                'language'   => 'es',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]);
        }


        // ─── Funcionarios (4) ────────────────────────────────
        $dependencias = ['Secretaría de Ambiente', 'AZCA', 'Secretaría de Movilidad', 'Comisaría Zonal'];
        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'tipo_id'    => 'Cedula',
                'nro_id'     => '170000030' . $i,
                'first_name' => "Funcionario{$i}",
                'last_name'  => 'DMQ',
                'role_name'  => 'Funcionario',
                'email'      => "funcionario{$i}@limpiaturincon.ec",
                'password'   => $defaultPassword,
                'phone'      => '096123456' . $i,
                'birthdate'  => '1982-0' . ($i % 9 + 1) . '-12',
                'gender'     => $i % 2 == 0 ? 'F' : 'M',
                'timezone'   => 'America/Guayaquil',
                'language'   => 'es',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]);
        }

        // ─── Vecinos (25, repartidos entre 5 barrios) ───────
        $nombres = [
            'María',
            'José',
            'Carlos',
            'Ana',
            'Luis',
            'Carmen',
            'Diego',
            'Patricia',
            'Jorge',
            'Lucía',
            'Pedro',
            'Rosa',
            'Fernando',
            'Gabriela',
            'Andrés',
            'Verónica',
            'Pablo',
            'Sofía',
            'Miguel',
            'Daniela',
            'Esteban',
            'Paola',
            'Ricardo',
            'Mónica',
            'Iván',
        ];
        $apellidos = [
            'Pérez',
            'Tapia',
            'Cevallos',
            'Morales',
            'Salazar',
            'Cárdenas',
            'Pazmiño',
            'Ron',
            'Vega',
            'Yépez',
            'Andrade',
            'Quishpe',
            'Tenorio',
            'Naranjo',
            'Vaca',
            'Bonilla',
            'Suárez',
            'Loachamín',
            'Toapanta',
            'Guamán',
            'Chávez',
            'Lara',
            'Mora',
            'Espín',
            'Sánchez',
        ];

        for ($i = 1; $i <= 25; $i++) {
            $nombre = $nombres[$i - 1];
            $apellido = $apellidos[$i - 1];

            User::create([
                'tipo_id'    => 'Cedula',
                'nro_id'     => '17100' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'first_name' => $nombre,
                'last_name'  => $apellido,
                'role_name'  => 'Vecino',
                'email'      => 'vecino' . $i . '@limpiaturincon.ec',
                'password'   => $defaultPassword,
                'phone'      => '098765' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'birthdate'  => sprintf('19%02d-%02d-%02d', 60 + ($i % 35), ($i % 12) + 1, ($i % 27) + 1),
                'gender'     => $i % 2 == 0 ? 'F' : 'M',
                'timezone'   => 'America/Guayaquil',
                'language'   => 'es',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]);
        }

        // 2. Recorremos los barrios usando lazy() para cuidar la memoria
        // Asegúrate de que $defaultPassword esté definida antes del bucle
        $defaultPassword = bcrypt('password123');

        // Usamos 'use ($defaultPassword)' para que la función anónima pueda leer esa variable
        Barrio::activos()->lazy()->each(function (Barrio $barrio) use ($defaultPassword) {

            $cantidadVecinos = rand(3, 8);

            // ─── Presidentes ───────────────────────────────────
            for ($i = 0; $i < $cantidadVecinos; $i++) {
                User::create([
                    'tipo_id'    => 'Cedula',
                    // Usamos $barrio->id para asegurar que la cédula y el email sean únicos por barrio
                    'nro_id'     => '17' . sprintf('%02d', $barrio->id) . '0010' . $i,
                    'first_name' => "Presidente{$i}",
                    'last_name'  => $barrio->id_DMQ,
                    'role_name'  => 'Presidente',
                    'email'      => "presidente{$i}_b{$barrio->id}@limpiaturincon.ec",
                    'password'   => $defaultPassword,
                    'phone'      => '098123456' . $i,
                    'birthdate'  => '1975-0' . ($i % 9 + 1) . '-05',
                    'gender'     => $i % 2 == 0 ? 'F' : 'M',
                    'timezone'   => 'America/Guayaquil',
                    'language'   => 'es',
                    'is_active'  => true,
                    'email_verified_at' => now(),
                ]);
            }

            // ─── Dirigentes ─────────────────────────────────────
            for ($i = 0; $i < $cantidadVecinos; $i++) {
                User::create([
                    'tipo_id'    => 'Cedula',
                    'nro_id'     => '17' . sprintf('%02d', $barrio->id) . '0020' . $i,
                    'first_name' => "Dirigente{$i}",
                    'last_name'  => $barrio->id_DMQ,
                    'role_name'  => 'Dirigente',
                    'email'      => "dirigente{$i}_b{$barrio->id}@limpiaturincon.ec",
                    'password'   => $defaultPassword,
                    'phone'      => '097123456' . $i,
                    'birthdate'  => '1978-0' . ($i % 9 + 1) . '-20',
                    'gender'     => $i % 2 == 0 ? 'M' : 'F',
                    'timezone'   => 'America/Guayaquil',
                    'language'   => 'es',
                    'is_active'  => true,
                    'email_verified_at' => now(),
                ]);
            }
        }); // <- Aquí se cierra correctamente el each()

    }
}
