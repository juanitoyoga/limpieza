<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterventionLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'level' => 1,
                'code' => 'INSPECTION',
                'name' => 'Inspección y diagnóstico',
                'type' => 'inspection',
                'description' => 'Visita, revisión y registro del estado de un espacio, elemento o infraestructura.',
                'specialist' => false,
                'equipment' => false,
                'authorization' => false,
                'order' => 1,
            ],
            [
                'level' => 2,
                'code' => 'BASIC',
                'name' => 'Intervención básica',
                'type' => 'basic',
                'description' => 'Limpieza, ajuste o reparación sencilla que no requiere desmontaje ni conocimientos especializados.',
                'specialist' => false,
                'equipment' => false,
                'authorization' => false,
                'order' => 2,
            ],
            [
                'level' => 3,
                'code' => 'PREVENTIVE',
                'name' => 'Mantenimiento preventivo',
                'type' => 'preventive',
                'description' => 'Actividad programada para evitar fallas, deterioro o pérdida de funcionalidad.',
                'specialist' => false,
                'equipment' => true,
                'authorization' => false,
                'order' => 3,
            ],
            [
                'level' => 4,
                'code' => 'CORRECTIVE',
                'name' => 'Mantenimiento correctivo',
                'type' => 'corrective',
                'description' => 'Reparación de un daño o falla existente para recuperar el funcionamiento normal.',
                'specialist' => false,
                'equipment' => true,
                'authorization' => false,
                'order' => 4,
            ],
            [
                'level' => 5,
                'code' => 'SPECIALIZED',
                'name' => 'Intervención especializada',
                'type' => 'specialized',
                'description' => 'Trabajo que requiere personal técnico cualificado, maquinaria o procedimientos específicos.',
                'specialist' => true,
                'equipment' => true,
                'authorization' => true,
                'order' => 5,
            ],
            [
                'level' => 6,
                'code' => 'EMERGENCY',
                'name' => 'Intervención de emergencia',
                'type' => 'emergency',
                'description' => 'Actuación inmediata ante un riesgo para personas, bienes, tránsito o infraestructura.',
                'specialist' => true,
                'equipment' => true,
                'authorization' => false,
                'order' => 6,
            ],
            [
                'level' => 7,
                'code' => 'MINOR_WORK',
                'name' => 'Obra menor',
                'type' => 'construction',
                'description' => 'Reparación, reposición o modificación física de una parte de la infraestructura.',
                'specialist' => true,
                'equipment' => true,
                'authorization' => true,
                'order' => 7,
            ],
            [
                'level' => 8,
                'code' => 'MAJOR_REPAIR',
                'name' => 'Reparación mayor',
                'type' => 'major_repair',
                'description' => 'Intervención de mayor alcance que requiere planificación, materiales y coordinación técnica.',
                'specialist' => true,
                'equipment' => true,
                'authorization' => true,
                'order' => 8,
            ],
            [
                'level' => 9,
                'code' => 'REPLACEMENT',
                'name' => 'Reposición integral',
                'type' => 'replacement',
                'description' => 'Retiro y sustitución completa de un elemento deteriorado o fuera de servicio.',
                'specialist' => true,
                'equipment' => true,
                'authorization' => true,
                'order' => 9,
            ],
            [
                'level' => 10,
                'code' => 'UPGRADE',
                'name' => 'Mejora o modernización',
                'type' => 'upgrade',
                'description' => 'Actualización o mejora de una infraestructura para aumentar su seguridad, capacidad o funcionalidad.',
                'specialist' => true,
                'equipment' => true,
                'authorization' => true,
                'order' => 10,
            ],
        ];

        $now = now();

        $rows = collect($levels)
            ->map(function (array $level) use ($now): array {
                return [
                    'level' => $level['level'],
                    'code' => $level['code'],
                    'name' => $level['name'],
                    'intervention_type' => $level['type'],
                    'description' => $level['description'],
                    'requires_specialist' => $level['specialist'],
                    'requires_equipment' => $level['equipment'],
                    'requires_authorization' => $level['authorization'],
                    'sort_order' => $level['order'],
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values()
            ->all();

        DB::table('intervention_levels')->upsert(
            $rows,
            ['code'],
            [
                'level',
                'name',
                'intervention_type',
                'description',
                'requires_specialist',
                'requires_equipment',
                'requires_authorization',
                'sort_order',
                'active',
                'updated_at',
            ]
        );
    }
}
