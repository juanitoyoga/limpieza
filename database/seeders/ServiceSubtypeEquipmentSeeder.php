<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSubtypeEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $subtypes = DB::table('service_subtypes')
            ->pluck('id', 'code');

        $equipment = DB::table('equipment')
            ->pluck('id', 'code');

        $relations = [
            /*
            |--------------------------------------------------------------------------
            | LIM-001 - Barrido manual de calles
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'LIM-001',
                'equipment' => 'HAND_BROOM',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Una escoba por trabajador o cuadrilla.',
            ],
            [
                'subtype' => 'LIM-001',
                'equipment' => 'DUSTPAN',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para recoger los residuos después del barrido.',
            ],
            [
                'subtype' => 'LIM-001',
                'equipment' => 'WASTE_BAGS',
                'quantity' => 10,
                'required' => true,
                'notes' => 'Cantidad estimada por jornada.',
            ],
            [
                'subtype' => 'LIM-001',
                'equipment' => 'WORK_GLOVES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],
            [
                'subtype' => 'LIM-001',
                'equipment' => 'REFLECTIVE_VEST',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Obligatorio durante trabajos en vías.',
            ],

            /*
            |--------------------------------------------------------------------------
            | LIM-005 - Baldeo manual
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'LIM-005',
                'equipment' => 'BUCKET',
                'quantity' => 2,
                'required' => true,
                'notes' => 'Para transportar agua y productos de limpieza.',
            ],
            [
                'subtype' => 'LIM-005',
                'equipment' => 'HAND_BRUSH',
                'quantity' => 2,
                'required' => true,
                'notes' => 'Para limpiar manchas y suciedad adherida.',
            ],
            [
                'subtype' => 'LIM-005',
                'equipment' => 'DETERGENT',
                'quantity' => 2,
                'required' => true,
                'notes' => 'Cantidad según el área de intervención.',
            ],
            [
                'subtype' => 'LIM-005',
                'equipment' => 'RUBBER_GLOVES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],

            /*
            |--------------------------------------------------------------------------
            | RES-001 - Recolección de residuos ordinarios
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'RES-001',
                'equipment' => 'WASTE_CART',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Carro para trasladar residuos dentro del sector.',
            ],
            [
                'subtype' => 'RES-001',
                'equipment' => 'WASTE_BAGS',
                'quantity' => 20,
                'required' => true,
                'notes' => 'Cantidad estimada por jornada.',
            ],
            [
                'subtype' => 'RES-001',
                'equipment' => 'WORK_GLOVES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],
            [
                'subtype' => 'RES-001',
                'equipment' => 'SAFETY_BOOTS',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],
            [
                'subtype' => 'RES-001',
                'equipment' => 'REFLECTIVE_VEST',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para trabajos próximos a vías.',
            ],

            /*
            |--------------------------------------------------------------------------
            | RES-003 - Retiro de escombros
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'RES-003',
                'equipment' => 'SHOVEL',
                'quantity' => 2,
                'required' => true,
                'notes' => 'Para cargar escombros y materiales sueltos.',
            ],
            [
                'subtype' => 'RES-003',
                'equipment' => 'WHEELBARROW',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para trasladar materiales hasta el vehículo.',
            ],
            [
                'subtype' => 'RES-003',
                'equipment' => 'DUMP_TRUCK',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Vehículo sujeto al volumen del material.',
            ],
            [
                'subtype' => 'RES-003',
                'equipment' => 'SAFETY_HELMET',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Obligatorio en zonas de obra o demolición.',
            ],
            [
                'subtype' => 'RES-003',
                'equipment' => 'SAFETY_GLASSES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Protección contra partículas.',
            ],

            /*
            |--------------------------------------------------------------------------
            | VIA-003 - Reparación de baches
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'VIA-003',
                'equipment' => 'SHOVEL',
                'quantity' => 2,
                'required' => true,
                'notes' => 'Para extender y manipular material.',
            ],
            [
                'subtype' => 'VIA-003',
                'equipment' => 'PICKAXE',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para remover material deteriorado.',
            ],
            [
                'subtype' => 'VIA-003',
                'equipment' => 'WHEELBARROW',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para transportar material de reparación.',
            ],
            [
                'subtype' => 'VIA-003',
                'equipment' => 'REFLECTIVE_VEST',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Obligatorio para trabajos en calzada.',
            ],
            [
                'subtype' => 'VIA-003',
                'equipment' => 'TRAFFIC_CONE',
                'quantity' => 6,
                'required' => true,
                'notes' => 'Si se incorpora posteriormente al catálogo de equipos.',
            ],

            /*
            |--------------------------------------------------------------------------
            | VER-001 - Corte de césped
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'VER-001',
                'equipment' => 'LAWN_MOWER',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Seleccionar el tipo según el tamaño del área.',
            ],
            [
                'subtype' => 'VER-001',
                'equipment' => 'BRUSHCUTTER',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Para bordes y zonas donde no entra el cortacésped.',
            ],
            [
                'subtype' => 'VER-001',
                'equipment' => 'LEAF_BLOWER',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Para retirar restos de césped.',
            ],
            [
                'subtype' => 'VER-001',
                'equipment' => 'SAFETY_GLASSES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Protección contra partículas proyectadas.',
            ],
            [
                'subtype' => 'VER-001',
                'equipment' => 'EAR_PROTECTION',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Recomendada para maquinaria motorizada.',
            ],

            /*
            |--------------------------------------------------------------------------
            | VER-002 - Desbroce de áreas verdes
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'VER-002',
                'equipment' => 'BRUSHCUTTER',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Uso exclusivo por personal capacitado.',
            ],
            [
                'subtype' => 'VER-002',
                'equipment' => 'MACHETE',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Para zonas de difícil acceso.',
            ],
            [
                'subtype' => 'VER-002',
                'equipment' => 'WORK_GLOVES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],
            [
                'subtype' => 'VER-002',
                'equipment' => 'SAFETY_GLASSES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Protección ocular obligatoria.',
            ],
            [
                'subtype' => 'VER-002',
                'equipment' => 'EAR_PROTECTION',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para trabajos con desbrozadora.',
            ],

            /*
            |--------------------------------------------------------------------------
            | ARB-002 - Poda de mantenimiento
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'ARB-002',
                'equipment' => 'PRUNING_SHEARS',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para ramas pequeñas.',
            ],
            [
                'subtype' => 'ARB-002',
                'equipment' => 'LONG_HANDLE_SHEARS',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Para ramas de mayor grosor.',
            ],
            [
                'subtype' => 'ARB-002',
                'equipment' => 'HAND_SAW',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Para ramas que no puedan cortarse con tijeras.',
            ],
            [
                'subtype' => 'ARB-002',
                'equipment' => 'WORK_GLOVES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],
            [
                'subtype' => 'ARB-002',
                'equipment' => 'SAFETY_GLASSES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Protección contra ramas y partículas.',
            ],

            /*
            |--------------------------------------------------------------------------
            | DRE-001 - Limpieza de sumideros
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'DRE-001',
                'equipment' => 'SHOVEL',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para retirar sedimentos y residuos.',
            ],
            [
                'subtype' => 'DRE-001',
                'equipment' => 'HAND_BRUSH',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para limpiar rejillas y bordes.',
            ],
            [
                'subtype' => 'DRE-001',
                'equipment' => 'WASTE_BAGS',
                'quantity' => 10,
                'required' => true,
                'notes' => 'Para retirar residuos extraídos.',
            ],
            [
                'subtype' => 'DRE-001',
                'equipment' => 'RUBBER_GLOVES',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Un par por trabajador.',
            ],
            [
                'subtype' => 'DRE-001',
                'equipment' => 'SAFETY_BOOTS',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Protección contra humedad y suciedad.',
            ],

            /*
            |--------------------------------------------------------------------------
            | ALU-003 - Cambio de luminaria
            |--------------------------------------------------------------------------
            */
            [
                'subtype' => 'ALU-003',
                'equipment' => 'LADDER',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Solo para alturas permitidas y condiciones seguras.',
            ],
            [
                'subtype' => 'ALU-003',
                'equipment' => 'WRENCH_SET',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para desmontar y fijar componentes.',
            ],
            [
                'subtype' => 'ALU-003',
                'equipment' => 'ELECTRIC_DRILL',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Según el tipo de fijación.',
            ],
            [
                'subtype' => 'ALU-003',
                'equipment' => 'SAFETY_HELMET',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Protección obligatoria durante el trabajo.',
            ],
            [
                'subtype' => 'ALU-003',
                'equipment' => 'REFLECTIVE_VEST',
                'quantity' => 1,
                'required' => true,
                'notes' => 'Para trabajos en espacios de circulación.',
            ],
            [
                'subtype' => 'ALU-003',
                'equipment' => 'MOBILE_PHONE',
                'quantity' => 1,
                'required' => false,
                'notes' => 'Para registrar la intervención y tomar fotografías.',
            ],
        ];

        $now = now();
        $rows = [];

        foreach ($relations as $relation) {
            $subtypeId = $subtypes->get($relation['subtype']);
            $equipmentId = $equipment->get($relation['equipment']);

            if (!$subtypeId) {
                throw new \RuntimeException(
                    "No existe el subtipo: {$relation['subtype']}"
                );
            }

            if (!$equipmentId) {
                throw new \RuntimeException(
                    "No existe el equipo: {$relation['equipment']}"
                );
            }

            $rows[] = [
                'service_subtype_id' => $subtypeId,
                'equipment_id' => $equipmentId,
                'quantity' => $relation['quantity'],
                'required' => $relation['required'],
                'notes' => $relation['notes'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('service_subtype_equipment')->upsert(
            $rows,
            [
                'service_subtype_id',
                'equipment_id',
            ],
            [
                'quantity',
                'required',
                'notes',
                'updated_at',
            ]
        );
    }
}
