<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'tipo_id' => 'Cedula',
            'nro_id' => '0102030405',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'role' => 'Funcionario',
            'transition_role' => null,
            'email' => 'admin@example.com',
            'password' => 'password', // se hashea automáticamente por el mutator
            'phone' => '+593987654321',
            'birthdate' => '1990-05-15',
            'gender' => 'M',
            'avatar' => null,
            'timezone' => 'America/Guayaquil',
            'language' => 'es',
            'last_login_at' => now(),
            'last_login_ip' => '127.0.0.1',
            'verification_token' => Str::random(60),
            'is_active' => true,
        ]);

        // Usuario vecino
        User::create([
            'tipo_id' => 'Pasaporte',
            'nro_id' => 'AB1234567',
            'first_name' => 'María',
            'last_name' => 'Gómez',
            'role' => 'Vecino',
            'transition_role' => null,
            'email' => 'maria@example.com',
            'password' => 'password',
            'phone' => '+593912345678',
            'birthdate' => '1985-08-20',
            'gender' => 'F',
            'avatar' => null,
            'timezone' => 'America/Guayaquil',
            'language' => 'es',
            'last_login_at' => now(),
            'last_login_ip' => '127.0.0.1',
            'verification_token' => Str::random(60),
            'is_active' => true,
        ]);

        // Generar varios usuarios aleatorios con factory
        User::factory()->count(10)->create();
    }
}
