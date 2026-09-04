<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipment = [
            /*
            |--------------------------------------------------------------------------
            | Herramientas manuales de limpieza
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'HAND_BROOM',
                'name' => 'Escoba manual',
                'type' => 'manual_tool',
                'description' => 'Herramienta para barrer calles, aceras y espacios públicos.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'STIFF_BROOM',
                'name' => 'Escoba de cerdas duras',
                'type' => 'manual_tool',
                'description' => 'Escoba para retirar tierra, residuos y suciedad adherida.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'DUSTPAN',
                'name' => 'Recogedor',
                'type' => 'manual_tool',
                'description' => 'Herramienta para recoger residuos después del barrido.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'HAND_BRUSH',
                'name' => 'Cepillo manual',
                'type' => 'manual_tool',
                'description' => 'Cepillo para limpiar superficies, rejillas y mobiliario.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'SHOVEL',
                'name' => 'Pala',
                'type' => 'manual_tool',
                'description' => 'Herramienta para recoger tierra, residuos, arena o escombros.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'SQUARE_SHOVEL',
                'name' => 'Pala cuadrada',
                'type' => 'manual_tool',
                'description' => 'Pala para cargar materiales y residuos sólidos.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'RAKE',
                'name' => 'Rastrillo',
                'type' => 'manual_tool',
                'description' => 'Herramienta para recoger hojas, césped y residuos vegetales.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'HOE',
                'name' => 'Azadón',
                'type' => 'manual_tool',
                'description' => 'Herramienta para remover tierra y realizar trabajos de jardinería.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'PICKAXE',
                'name' => 'Pico',
                'type' => 'manual_tool',
                'description' => 'Herramienta para romper terrenos duros, pavimentos o materiales compactos.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'CROWBAR',
                'name' => 'Barreta',
                'type' => 'manual_tool',
                'description' => 'Herramienta para hacer palanca, levantar o separar materiales.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'WHEELBARROW',
                'name' => 'Carretilla',
                'type' => 'manual_tool',
                'description' => 'Equipo manual para transportar residuos, tierra, herramientas o materiales.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'BUCKET',
                'name' => 'Cubo',
                'type' => 'manual_tool',
                'description' => 'Recipiente para transportar agua, productos o materiales de limpieza.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'LADDER',
                'name' => 'Escalera',
                'type' => 'manual_tool',
                'description' => 'Equipo para realizar trabajos en altura baja o media.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'HAND_SAW',
                'name' => 'Sierra manual',
                'type' => 'manual_tool',
                'description' => 'Herramienta para cortar ramas, madera o materiales ligeros.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'HAMMER',
                'name' => 'Martillo',
                'type' => 'manual_tool',
                'description' => 'Herramienta para golpear, fijar o desmontar elementos.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'PLIERS',
                'name' => 'Alicates',
                'type' => 'manual_tool',
                'description' => 'Herramienta para sujetar, cortar o manipular piezas pequeñas.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'WRENCH_SET',
                'name' => 'Juego de llaves',
                'type' => 'manual_tool',
                'description' => 'Conjunto de herramientas para ajustar y desmontar piezas.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | Jardinería y poda
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'PRUNING_SHEARS',
                'name' => 'Tijeras de poda',
                'type' => 'gardening_supply',
                'description' => 'Tijeras para cortar ramas pequeñas y realizar poda manual.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'LONG_HANDLE_SHEARS',
                'name' => 'Tijeras de dos manos',
                'type' => 'gardening_supply',
                'description' => 'Tijeras de mango largo para cortar ramas de mayor grosor.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'HEDGE_SHEARS',
                'name' => 'Tijeras cortasetos',
                'type' => 'gardening_supply',
                'description' => 'Herramienta manual para formar y mantener setos y arbustos.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'MACHETE',
                'name' => 'Machete',
                'type' => 'manual_tool',
                'description' => 'Herramienta de corte para desbroce y vegetación densa.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'GARDEN_HOSE',
                'name' => 'Manguera',
                'type' => 'gardening_supply',
                'description' => 'Manguera para riego y limpieza de superficies.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'WATERING_CAN',
                'name' => 'Regadera',
                'type' => 'gardening_supply',
                'description' => 'Recipiente manual para regar plantas y jardines.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'TREE_CLIMBING_KIT',
                'name' => 'Equipo de trabajo en altura',
                'type' => 'safety_equipment',
                'description' => 'Conjunto de elementos para trabajos autorizados de poda en altura.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | Herramientas eléctricas y maquinaria
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'BRUSHCUTTER',
                'name' => 'Desbrozadora',
                'type' => 'power_tool',
                'description' => 'Máquina para cortar maleza, hierba alta y vegetación densa.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'LAWN_MOWER',
                'name' => 'Cortacésped',
                'type' => 'power_tool',
                'description' => 'Máquina para cortar y mantener superficies de césped.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'HEDGE_TRIMMER',
                'name' => 'Cortasetos motorizado',
                'type' => 'power_tool',
                'description' => 'Máquina para recortar setos y arbustos.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'CHAINSAW',
                'name' => 'Motosierra',
                'type' => 'power_tool',
                'description' => 'Máquina para cortar ramas y troncos de mayor tamaño.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'LEAF_BLOWER',
                'name' => 'Sopladora',
                'type' => 'power_tool',
                'description' => 'Equipo para desplazar hojas y residuos ligeros.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'PRESSURE_WASHER',
                'name' => 'Hidrolavadora',
                'type' => 'machinery',
                'description' => 'Equipo para limpiar superficies utilizando agua a presión.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'INDUSTRIAL_VACUUM',
                'name' => 'Aspiradora industrial',
                'type' => 'machinery',
                'description' => 'Equipo para aspirar polvo, residuos sólidos y líquidos.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'MECHANICAL_SWEEPER',
                'name' => 'Barredora mecánica',
                'type' => 'machinery',
                'description' => 'Máquina para barrer calles, aceras y superficies amplias.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'WATER_PUMP',
                'name' => 'Bomba de agua',
                'type' => 'machinery',
                'description' => 'Equipo para extraer o trasladar agua en trabajos de drenaje o riego.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'ELECTRIC_DRILL',
                'name' => 'Taladro eléctrico',
                'type' => 'power_tool',
                'description' => 'Herramienta para perforar y fijar elementos.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'ANGLE_GRINDER',
                'name' => 'Amoladora',
                'type' => 'power_tool',
                'description' => 'Herramienta para cortar, desbastar o pulir materiales.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'PORTABLE_GENERATOR',
                'name' => 'Generador eléctrico',
                'type' => 'machinery',
                'description' => 'Equipo para suministrar energía eléctrica en lugares sin conexión disponible.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | Equipos de protección personal
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'SAFETY_HELMET',
                'name' => 'Casco de seguridad',
                'type' => 'safety_equipment',
                'description' => 'Protección de la cabeza frente a golpes y caída de objetos.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'SAFETY_GLASSES',
                'name' => 'Gafas de protección',
                'type' => 'safety_equipment',
                'description' => 'Protección ocular contra polvo, partículas y salpicaduras.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'WORK_GLOVES',
                'name' => 'Guantes de trabajo',
                'type' => 'safety_equipment',
                'description' => 'Protección de las manos durante trabajos de limpieza y mantenimiento.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'RUBBER_GLOVES',
                'name' => 'Guantes impermeables',
                'type' => 'safety_equipment',
                'description' => 'Protección de las manos frente a agua, humedad y productos de limpieza.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'SAFETY_BOOTS',
                'name' => 'Botas de seguridad',
                'type' => 'safety_equipment',
                'description' => 'Protección de los pies frente a humedad, golpes y objetos pesados.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'REFLECTIVE_VEST',
                'name' => 'Chaleco reflectante',
                'type' => 'safety_equipment',
                'description' => 'Prenda de alta visibilidad para trabajos en vías y espacios públicos.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'EAR_PROTECTION',
                'name' => 'Protección auditiva',
                'type' => 'safety_equipment',
                'description' => 'Protección contra niveles elevados de ruido producidos por maquinaria.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'DUST_MASK',
                'name' => 'Mascarilla antipolvo',
                'type' => 'safety_equipment',
                'description' => 'Protección respiratoria frente a polvo y partículas suspendidas.',
                'consumable' => true,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'SAFETY_HARNESS',
                'name' => 'Arnés de seguridad',
                'type' => 'safety_equipment',
                'description' => 'Equipo de protección para trabajos autorizados en altura.',
                'consumable' => false,
                'training' => true,
                'safety' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | Materiales y suministros
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'WASTE_BAGS',
                'name' => 'Bolsas para residuos',
                'type' => 'cleaning_supply',
                'description' => 'Bolsas para recoger y transportar residuos.',
                'consumable' => true,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'ABSORBENT_MATERIAL',
                'name' => 'Material absorbente',
                'type' => 'cleaning_supply',
                'description' => 'Material para absorber derrames de líquidos.',
                'consumable' => true,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'DETERGENT',
                'name' => 'Detergente',
                'type' => 'cleaning_supply',
                'description' => 'Producto para limpieza de superficies y elementos urbanos.',
                'consumable' => true,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'DISINFECTANT',
                'name' => 'Desinfectante',
                'type' => 'cleaning_supply',
                'description' => 'Producto para desinfección de superficies y espacios comunitarios.',
                'consumable' => true,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'BLEACH',
                'name' => 'Lejía o cloro',
                'type' => 'cleaning_supply',
                'description' => 'Producto químico utilizado para limpieza y desinfección.',
                'consumable' => true,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'CLEANING_CLOTH',
                'name' => 'Paños de limpieza',
                'type' => 'cleaning_supply',
                'description' => 'Paños para limpiar mobiliario, superficies y elementos comunitarios.',
                'consumable' => true,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'ROPE',
                'name' => 'Cuerda de seguridad',
                'type' => 'safety_equipment',
                'description' => 'Elemento auxiliar para señalización, sujeción o trabajos autorizados.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],

            /*
            |--------------------------------------------------------------------------
            | Transporte y comunicación
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'WASTE_CART',
                'name' => 'Carro recolector',
                'type' => 'transport',
                'description' => 'Carro manual para transportar residuos y herramientas.',
                'consumable' => false,
                'training' => false,
                'safety' => true,
            ],
            [
                'code' => 'UTILITY_VEHICLE',
                'name' => 'Vehículo utilitario',
                'type' => 'transport',
                'description' => 'Vehículo para movilizar cuadrillas, equipos y materiales.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'DUMP_TRUCK',
                'name' => 'Camión volquete',
                'type' => 'transport',
                'description' => 'Vehículo para transportar tierra, escombros y residuos voluminosos.',
                'consumable' => false,
                'training' => true,
                'safety' => true,
            ],
            [
                'code' => 'RADIO',
                'name' => 'Radio de comunicación',
                'type' => 'communication',
                'description' => 'Equipo para comunicación entre cuadrillas y responsables.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'MOBILE_PHONE',
                'name' => 'Teléfono móvil operativo',
                'type' => 'communication',
                'description' => 'Dispositivo para comunicación, registro fotográfico y reporte de incidencias.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
            [
                'code' => 'TRAFFIC_CONE',
                'name' => 'Cono de señalización',
                'type' => 'safety_equipment',
                'description' => 'Elemento para delimitar y señalizar zonas de trabajo.',
                'consumable' => false,
                'training' => false,
                'safety' => false,
            ],
        ];

        $now = now();

        $rows = collect($equipment)
            ->values()
            ->map(function (array $item, int $index) use ($now): array {
                return [
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'equipment_type' => $item['type'],
                    'description' => $item['description'],
                    'is_consumable' => $item['consumable'],
                    'requires_training' => $item['training'],
                    'requires_safety_equipment' => $item['safety'],
                    'sort_order' => $index + 1,
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->all();

        DB::table('equipment')->upsert(
            $rows,
            ['code'],
            [
                'name',
                'equipment_type',
                'description',
                'is_consumable',
                'requires_training',
                'requires_safety_equipment',
                'sort_order',
                'active',
                'updated_at',
            ]
        );
    }
}
