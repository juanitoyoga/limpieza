<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceScopeSeeder extends Seeder
{
    public function run(): void
    {
        $scopes = [
            /*
            |--------------------------------------------------------------------------
            | Vías y circulación
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'STREET',
                'name' => 'Calle',
                'scope_type' => 'road',
                'description' => 'Vía pública urbana destinada a la circulación y acceso a viviendas o comercios.',
                'order' => 1,
            ],
            [
                'code' => 'AVENUE',
                'name' => 'Avenida',
                'scope_type' => 'road',
                'description' => 'Vía urbana principal con circulación vehicular y peatonal.',
                'order' => 2,
            ],
            [
                'code' => 'BOULEVARD',
                'name' => 'Bulevar',
                'scope_type' => 'road',
                'description' => 'Vía amplia con áreas peatonales, vegetación o separación central.',
                'order' => 3,
            ],
            [
                'code' => 'ROADWAY',
                'name' => 'Calzada',
                'scope_type' => 'road',
                'description' => 'Superficie destinada principalmente al tránsito de vehículos.',
                'order' => 4,
            ],
            [
                'code' => 'SIDEWALK',
                'name' => 'Acera o vereda',
                'scope_type' => 'pedestrian',
                'description' => 'Espacio lateral de la vía destinado al tránsito peatonal.',
                'order' => 5,
            ],
            [
                'code' => 'PEDESTRIAN_PATH',
                'name' => 'Sendero peatonal',
                'scope_type' => 'pedestrian',
                'description' => 'Camino destinado al tránsito de peatones dentro de parques o zonas verdes.',
                'order' => 6,
            ],
            [
                'code' => 'CYCLE_PATH',
                'name' => 'Ciclovía',
                'scope_type' => 'pedestrian',
                'description' => 'Vía o espacio destinado a la circulación de bicicletas.',
                'order' => 7,
            ],
            [
                'code' => 'CROSSWALK',
                'name' => 'Paso peatonal',
                'scope_type' => 'pedestrian',
                'description' => 'Zona señalizada para el cruce seguro de peatones.',
                'order' => 8,
            ],
            [
                'code' => 'STAIRWAY',
                'name' => 'Escalera pública',
                'scope_type' => 'pedestrian',
                'description' => 'Escalera de uso público que conecta diferentes niveles.',
                'order' => 9,
            ],
            [
                'code' => 'RAMP',
                'name' => 'Rampa peatonal',
                'scope_type' => 'pedestrian',
                'description' => 'Superficie inclinada destinada a facilitar el acceso peatonal.',
                'order' => 10,
            ],

            /*
            |--------------------------------------------------------------------------
            | Plazas, parques y áreas verdes
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'PUBLIC_SQUARE',
                'name' => 'Plaza pública',
                'scope_type' => 'public_space',
                'description' => 'Espacio público abierto destinado al encuentro, descanso y actividades comunitarias.',
                'order' => 11,
            ],
            [
                'code' => 'SMALL_SQUARE',
                'name' => 'Plazoleta',
                'scope_type' => 'public_space',
                'description' => 'Espacio público de menor tamaño destinado a estancia y encuentro.',
                'order' => 12,
            ],
            [
                'code' => 'URBAN_PARK',
                'name' => 'Parque urbano',
                'scope_type' => 'green_area',
                'description' => 'Espacio verde público destinado a recreación, descanso y convivencia.',
                'order' => 13,
            ],
            [
                'code' => 'NEIGHBORHOOD_PARK',
                'name' => 'Parque barrial',
                'scope_type' => 'green_area',
                'description' => 'Parque de uso principal para los habitantes del barrio.',
                'order' => 14,
            ],
            [
                'code' => 'GARDEN',
                'name' => 'Jardín público',
                'scope_type' => 'green_area',
                'description' => 'Área ajardinada con plantas ornamentales y espacios de estancia.',
                'order' => 15,
            ],
            [
                'code' => 'GREEN_AREA',
                'name' => 'Zona verde',
                'scope_type' => 'green_area',
                'description' => 'Área cubierta por césped, plantas, arbustos o vegetación.',
                'order' => 16,
            ],
            [
                'code' => 'GRASS_AREA',
                'name' => 'Área de césped',
                'scope_type' => 'green_area',
                'description' => 'Superficie cubierta principalmente por césped.',
                'order' => 17,
            ],
            [
                'code' => 'FLOWER_BED',
                'name' => 'Parterre o jardinera',
                'scope_type' => 'green_area',
                'description' => 'Espacio delimitado para plantas, flores y vegetación ornamental.',
                'order' => 18,
            ],
            [
                'code' => 'TREE_AREA',
                'name' => 'Zona arbolada',
                'scope_type' => 'green_area',
                'description' => 'Área con presencia significativa de árboles.',
                'order' => 19,
            ],
            [
                'code' => 'MEDIAN',
                'name' => 'Camellón o separador vial',
                'scope_type' => 'green_area',
                'description' => 'Franja central o lateral que separa carriles y puede contener vegetación.',
                'order' => 20,
            ],
            [
                'code' => 'ROUNDABOUT',
                'name' => 'Glorieta o rotonda',
                'scope_type' => 'road',
                'description' => 'Intersección circular con área central ajardinada o pavimentada.',
                'order' => 21,
            ],

            /*
            |--------------------------------------------------------------------------
            | Recreación y deporte
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'PLAYGROUND',
                'name' => 'Zona infantil',
                'scope_type' => 'recreation',
                'description' => 'Área equipada con juegos y elementos recreativos para niños.',
                'order' => 22,
            ],
            [
                'code' => 'SPORTS_COURT',
                'name' => 'Cancha deportiva',
                'scope_type' => 'sports',
                'description' => 'Superficie destinada a la práctica de deportes.',
                'order' => 23,
            ],
            [
                'code' => 'SPORTS_FIELD',
                'name' => 'Campo deportivo',
                'scope_type' => 'sports',
                'description' => 'Área deportiva abierta para fútbol u otras actividades.',
                'order' => 24,
            ],
            [
                'code' => 'SPORTS_TRACK',
                'name' => 'Pista deportiva',
                'scope_type' => 'sports',
                'description' => 'Pista destinada a actividades deportivas o recreativas.',
                'order' => 25,
            ],
            [
                'code' => 'OUTDOOR_GYM',
                'name' => 'Gimnasio al aire libre',
                'scope_type' => 'sports',
                'description' => 'Zona equipada con aparatos deportivos de uso público.',
                'order' => 26,
            ],
            [
                'code' => 'SPORTS_LOCKER_ROOM',
                'name' => 'Vestuario deportivo',
                'scope_type' => 'building',
                'description' => 'Espacio destinado al cambio de ropa y preparación de usuarios deportivos.',
                'order' => 27,
            ],

            /*
            |--------------------------------------------------------------------------
            | Edificios y equipamientos comunitarios
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'COMMUNITY_CENTER',
                'name' => 'Centro comunitario',
                'scope_type' => 'building',
                'description' => 'Edificio destinado a actividades y servicios comunitarios.',
                'order' => 28,
            ],
            [
                'code' => 'COMMUNITY_HALL',
                'name' => 'Salón comunitario',
                'scope_type' => 'building',
                'description' => 'Sala utilizada para reuniones y actividades del barrio.',
                'order' => 29,
            ],
            [
                'code' => 'NEIGHBORHOOD_OFFICE',
                'name' => 'Oficina barrial',
                'scope_type' => 'building',
                'description' => 'Espacio destinado a la administración o atención del barrio.',
                'order' => 30,
            ],
            [
                'code' => 'PUBLIC_TOILET',
                'name' => 'Baño público',
                'scope_type' => 'building',
                'description' => 'Instalación sanitaria de uso público o comunitario.',
                'order' => 31,
            ],
            [
                'code' => 'COMMUNITY_KITCHEN',
                'name' => 'Cocina comunitaria',
                'scope_type' => 'building',
                'description' => 'Espacio equipado para preparación de alimentos comunitarios.',
                'order' => 32,
            ],
            [
                'code' => 'MARKET',
                'name' => 'Mercado o feria',
                'scope_type' => 'commercial',
                'description' => 'Espacio destinado a actividades comerciales, ferias o mercados barriales.',
                'order' => 33,
            ],
            [
                'code' => 'BUS_STOP',
                'name' => 'Parada de transporte',
                'scope_type' => 'transport',
                'description' => 'Lugar destinado al ascenso y descenso de pasajeros.',
                'order' => 34,
            ],
            [
                'code' => 'PARKING_AREA',
                'name' => 'Estacionamiento comunitario',
                'scope_type' => 'transport',
                'description' => 'Área destinada al estacionamiento de vehículos.',
                'order' => 35,
            ],

            /*
            |--------------------------------------------------------------------------
            | Drenaje, agua y saneamiento
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'DRAIN',
                'name' => 'Sumidero',
                'scope_type' => 'drainage',
                'description' => 'Punto de captación de agua superficial.',
                'order' => 36,
            ],
            [
                'code' => 'DRAIN_GRATE',
                'name' => 'Rejilla de drenaje',
                'scope_type' => 'drainage',
                'description' => 'Rejilla que permite la entrada de agua al sistema de drenaje.',
                'order' => 37,
            ],
            [
                'code' => 'DITCH',
                'name' => 'Cuneta',
                'scope_type' => 'drainage',
                'description' => 'Canal lateral destinado a conducir aguas superficiales.',
                'order' => 38,
            ],
            [
                'code' => 'GUTTER',
                'name' => 'Canaleta',
                'scope_type' => 'drainage',
                'description' => 'Canal para recoger y conducir agua de lluvia.',
                'order' => 39,
            ],
            [
                'code' => 'DOWNPIPE',
                'name' => 'Bajante de aguas lluvias',
                'scope_type' => 'drainage',
                'description' => 'Conducto vertical para evacuar agua desde una cubierta.',
                'order' => 40,
            ],
            [
                'code' => 'SEWER_CHAMBER',
                'name' => 'Cámara de inspección',
                'scope_type' => 'sanitation',
                'description' => 'Registro que permite inspeccionar o mantener una red sanitaria.',
                'order' => 41,
            ],
            [
                'code' => 'SEWER_LINE',
                'name' => 'Red de alcantarillado',
                'scope_type' => 'sanitation',
                'description' => 'Conjunto de tuberías y elementos para conducir aguas residuales.',
                'order' => 42,
            ],
            [
                'code' => 'WATER_TANK',
                'name' => 'Depósito de agua',
                'scope_type' => 'water',
                'description' => 'Depósito destinado al almacenamiento de agua para riego o uso comunitario.',
                'order' => 43,
            ],

            /*
            |--------------------------------------------------------------------------
            | Mobiliario e infraestructura urbana
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'URBAN_BENCH',
                'name' => 'Banco público',
                'scope_type' => 'urban_furniture',
                'description' => 'Asiento instalado en calles, plazas, parques u otros espacios públicos.',
                'order' => 44,
            ],
            [
                'code' => 'WASTE_BIN',
                'name' => 'Papelera pública',
                'scope_type' => 'waste_point',
                'description' => 'Recipiente destinado al depósito de residuos pequeños.',
                'order' => 45,
            ],
            [
                'code' => 'WASTE_CONTAINER',
                'name' => 'Contenedor de residuos',
                'scope_type' => 'waste_point',
                'description' => 'Contenedor destinado al almacenamiento temporal de residuos.',
                'order' => 46,
            ],
            [
                'code' => 'STREET_LIGHT',
                'name' => 'Punto de alumbrado',
                'scope_type' => 'lighting',
                'description' => 'Poste, luminaria o conjunto destinado al alumbrado público.',
                'order' => 47,
            ],
            [
                'code' => 'FENCE',
                'name' => 'Valla o cerramiento',
                'scope_type' => 'urban_furniture',
                'description' => 'Elemento utilizado para delimitar o proteger un espacio.',
                'order' => 48,
            ],
            [
                'code' => 'HANDRAIL',
                'name' => 'Barandilla o pasamanos',
                'scope_type' => 'urban_furniture',
                'description' => 'Elemento de apoyo y protección instalado en escaleras o desniveles.',
                'order' => 49,
            ],
            [
                'code' => 'BOLLARD',
                'name' => 'Bolardo',
                'scope_type' => 'urban_furniture',
                'description' => 'Elemento que controla o impide el acceso de vehículos a zonas peatonales.',
                'order' => 50,
            ],
            [
                'code' => 'PUBLIC_FOUNTAIN',
                'name' => 'Fuente pública',
                'scope_type' => 'urban_furniture',
                'description' => 'Fuente ornamental o de suministro de agua para uso público.',
                'order' => 51,
            ],
            [
                'code' => 'SIGNAGE',
                'name' => 'Señalización urbana',
                'scope_type' => 'signage',
                'description' => 'Señales informativas, preventivas, reglamentarias o direccionales.',
                'order' => 52,
            ],
            [
                'code' => 'GRAFFITI_SURFACE',
                'name' => 'Muro o superficie con grafiti',
                'scope_type' => 'surface',
                'description' => 'Muro, fachada o elemento urbano afectado por pintadas o grafitis.',
                'order' => 53,
            ],

            /*
            |--------------------------------------------------------------------------
            | Zonas especiales
            |--------------------------------------------------------------------------
            */
            [
                'code' => 'VACANT_LOT',
                'name' => 'Lote o terreno baldío',
                'scope_type' => 'land',
                'description' => 'Terreno sin edificación que requiere limpieza o mantenimiento.',
                'order' => 54,
            ],
            [
                'code' => 'RIVER_BANK',
                'name' => 'Ribera o margen de río',
                'scope_type' => 'environmental',
                'description' => 'Zona próxima a ríos, quebradas u otros cuerpos de agua.',
                'order' => 55,
            ],
            [
                'code' => 'CHANNEL',
                'name' => 'Canal abierto',
                'scope_type' => 'drainage',
                'description' => 'Canal destinado a conducir aguas pluviales o de riego.',
                'order' => 56,
            ],
            [
                'code' => 'SLOPE',
                'name' => 'Talud o ladera',
                'scope_type' => 'land',
                'description' => 'Superficie inclinada susceptible a erosión, deslizamientos o acumulación de residuos.',
                'order' => 57,
            ],
            [
                'code' => 'CONSTRUCTION_ZONE',
                'name' => 'Zona de obra',
                'scope_type' => 'construction',
                'description' => 'Área donde se ejecutan trabajos de construcción, reparación o demolición.',
                'order' => 58,
            ],
            [
                'code' => 'EMERGENCY_ZONE',
                'name' => 'Zona de emergencia',
                'scope_type' => 'emergency',
                'description' => 'Área afectada por una situación urgente o de riesgo.',
                'order' => 59,
            ],
            [
                'code' => 'WHOLE_NEIGHBORHOOD',
                'name' => 'Barrio completo',
                'scope_type' => 'territory',
                'description' => 'Ámbito general que comprende todo el territorio del barrio.',
                'order' => 60,
            ],
        ];

        $now = now();

        $rows = collect($scopes)
            ->map(function (array $scope) use ($now): array {
                return [
                    'code' => $scope['code'],
                    'name' => $scope['name'],
                    'scope_type' => $scope['scope_type'],
                    'description' => $scope['description'],
                    'sort_order' => $scope['order'],
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values()
            ->all();

        DB::table('service_scopes')->upsert(
            $rows,
            ['code'],
            [
                'name',
                'scope_type',
                'description',
                'sort_order',
                'active',
                'updated_at',
            ]
        );
    }
}
