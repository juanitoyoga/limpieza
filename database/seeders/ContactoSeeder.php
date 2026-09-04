<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener todos los proveedores existentes
        $proveedores = Proveedor::all();

        if ($proveedores->isEmpty()) {
            $this->command->warn('No hay proveedores registrados. Ejecute primero el ProveedorSeeder.');
            return;
        }

        $cargosPrincipales = [
            'Gerente Comercial',
            'Director de Ventas',
            'Gerente General',
            'Jefe de Operaciones',
            'Representante Legal'
        ];

        $cargosSecundarios = [
            'Ejecutivo de Cuentas',
            'Asesor Técnico',
            'Coordinador de Logística',
            'Asistente Comercial',
            'Jefe de Bodega y Despachos'
        ];

        DB::transaction(function () use ($proveedores, $cargosPrincipales, $cargosSecundarios) {
            foreach ($proveedores as $proveedor) {

                // ==========================================
                // 1. CONTACTO PRINCIPAL (Obligatorio 1 por Proveedor)
                // ==========================================
                $nombrePrincipal = fake()->firstName();
                $apellidoPrincipal = fake()->lastName() . ' ' . fake()->lastName();
                // Extraer el dominio del correo del proveedor de forma segura
                $dominio = str_contains($proveedor->email ?? '', '@')
                    ? substr(strrchr($proveedor->email, "@"), 1)
                    : 'empresa.com';

                $emailPrincipal = strtolower(
                    substr($nombrePrincipal, 0, 1) .
                        explode(' ', $apellidoPrincipal)[0] .
                        '@' . $dominio
                );
                Contacto::firstOrCreate(
                    [
                        'proveedor_id' => $proveedor->id,
                        'es_principal' => true,
                    ],
                    [
                        'tipo_id'      => 1, // 1 = Cédula de Identidad
                        'nro_id'       => '17' . fake()->numerify('########'),
                        'first_name'   => $nombrePrincipal,
                        'last_name'    => $apellidoPrincipal,
                        'email'        => $emailPrincipal,
                        'phone'        => '09' . fake()->numerify('########'),
                        'cargo'        => fake()->randomElement($cargosPrincipales),
                        'usa_app'      => true,
                        'is_active'    => true,
                    ]
                );

                // ==========================================
                // 2. CONTACTOS SECUNDARIOS (1 o 2 adicionales)
                // ==========================================
                $cantidadSecundarios = fake()->numberBetween(1, 2);

                for ($i = 0; $i < $cantidadSecundarios; $i++) {
                    $nombreSecundario = fake()->firstName();
                    $apellidoSecundario = fake()->lastName() . ' ' . fake()->lastName();
                    // Extraer el dominio del correo del proveedor de forma segura
                    $dominio = str_contains($proveedor->email ?? '', '@')
                        ? substr(strrchr($proveedor->email, "@"), 1)
                        : 'empresa.com';

                    $emailSecundario = strtolower(
                        substr($nombreSecundario, 0, 1) .
                            explode(' ', $apellidoSecundario)[0] .
                            $i . '@' . $dominio
                    );


                    Contacto::firstOrCreate(
                        [
                            'proveedor_id' => $proveedor->id,
                            'email'        => $emailSecundario,
                        ],
                        [
                            'tipo_id'      => 1,
                            'nro_id'       => '17' . fake()->numerify('########'),
                            'first_name'   => $nombreSecundario,
                            'last_name'    => $apellidoSecundario,
                            'phone'        => '09' . fake()->numerify('########'),
                            'cargo'        => fake()->randomElement($cargosSecundarios),
                            'es_principal' => false,
                            'usa_app'      => fake()->boolean(60), // 60% de probabilidad de usar la app
                            'is_active'    => true,
                        ]
                    );
                }
            }
        });

        $this->command->info('Seeder de Contactos ejecutado exitosamente.');
    }
}
