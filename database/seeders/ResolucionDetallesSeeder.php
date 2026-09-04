<?php

namespace Database\Seeders;

use App\Models\CatalogoServicios;
use App\Models\Resolucion;
use App\Models\ResolucionParticipante;
use App\Models\ResolucionServicio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResolucionDetallesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cargar las resoluciones que necesitan detalles
        $resoluciones = Resolucion::all();

        if ($resoluciones->isEmpty()) {
            $this->command->warn('No se encontraron resoluciones para poblar detalles.');
            return;
        }

        // 2. Obtener listas de usuarios y servicios disponibles
        $users = User::where('is_active', true)->get();
        $servicios = CatalogoServicios::activos()->get();

        if ($users->isEmpty() || $servicios->isEmpty()) {
            $this->command->error('Se requieren usuarios activos y servicios en catálogo para ejecutar este seeder.');
            return;
        }

        $cargosPuestos = [
            'Presidente del Comité',
            'Secretario',
            'Tesorería',
            'Vocal de Seguridad',
            'Vocal de Obras Sociales',
            'Dirigente Vecinal',
            'Representante Comunitario'
        ];

        $prioridades = [
            ResolucionServicio::PRIORIDAD_BAJA,
            ResolucionServicio::PRIORIDAD_MEDIA,
            ResolucionServicio::PRIORIDAD_ALTA,
            ResolucionServicio::PRIORIDAD_URGENTE,
        ];

        DB::transaction(function () use ($resoluciones, $users, $servicios, $cargosPuestos, $prioridades) {
            foreach ($resoluciones as $resolucion) {

                // ==========================================
                // 1. POBLAR RESOLUCION_PARTICIPANTES
                // ==========================================
                // Cantidad esperada según el campo numero_firmas (por defecto min 1)
                $cantidadParticipantes = max((int) $resolucion->numero_firmas, 1);

                // Tomamos $cantidadParticipantes usuarios de forma aleatoria sin repetir en la misma resolución
                $participantesElegidos = $users->random(min($cantidadParticipantes, $users->count()));

                $ordenFirma = 1;
                foreach ($participantesElegidos as $user) {
                    ResolucionParticipante::firstOrCreate(
                        [
                            'resolucion_id' => $resolucion->id,
                            'user_id'       => $user->id,
                        ],
                        [
                            'nombre_firmante'     => $user->full_name,
                            'documento_identidad' => $user->nro_id ?? '17' . fake()->numerify('########'),
                            'cargo'               => fake()->randomElement($cargosPuestos),
                            'orden_firma'         => $ordenFirma++,
                        ]
                    );
                }

                // ==========================================
                // 2. POBLAR RESOLUCION_SERVICIOS
                // ==========================================
                // Cantidad esperada según el campo numero_servicios (por defecto min 1)
                $cantidadServicios = max((int) $resolucion->numero_servicios, 1);

                // Tomamos $cantidadServicios items del catálogo de forma aleatoria sin repetir
                $serviciosElegidos = $servicios->random(min($cantidadServicios, $servicios->count()));

                foreach ($serviciosElegidos as $servicio) {
                    ResolucionServicio::firstOrCreate(
                        [
                            'resolucion_id'        => $resolucion->id,
                            'catalogo_servicio_id' => $servicio->id,
                        ],
                        [
                            'cantidad'       => fake()->numberBetween(1, 10),
                            'prioridad'      => fake()->randomElement($prioridades),
                            'observaciones'  => fake()->optional(0.7)->sentence(),
                            'estado'         => $resolucion->auth_status ?? ResolucionServicio::ESTADO_PENDIENTE,
                            'costo_unitario' => $servicio->costo_referencial, // Snapshot del costo
                        ]
                    );
                }
            }
        });

        $this->command->info('Seeder de resolucion_participantes y resolucion_servicios completado exitosamente.');
    }
}
