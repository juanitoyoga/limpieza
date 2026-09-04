<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSubtypeSeeder extends Seeder
{
    public function run(): void
    {
        $serviceTypes = ServiceType::query()
            ->pluck('id', 'code');

        $subtypes = [
            /*
            |--------------------------------------------------------------------------
            | LIM - Limpieza urbana
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'LIM',
                'code' => 'LIM-001',
                'name' => 'Barrido manual de calles',
                'description' => 'Barrido manual de calles, aceras, bordillos y zonas peatonales.',
                'order' => 1,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-002',
                'name' => 'Barrido mecánico de calles',
                'description' => 'Limpieza de calzadas mediante maquinaria barredora.',
                'order' => 2,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-003',
                'name' => 'Barrido mecánico de aceras',
                'description' => 'Limpieza mecanizada de aceras, plazas y zonas peatonales.',
                'order' => 3,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-004',
                'name' => 'Barrido mixto',
                'description' => 'Limpieza realizada mediante combinación de medios manuales y mecánicos.',
                'order' => 4,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-005',
                'name' => 'Baldeo manual',
                'description' => 'Lavado manual de aceras, plazas y superficies públicas.',
                'order' => 5,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-006',
                'name' => 'Baldeo mecánico',
                'description' => 'Lavado de superficies mediante vehículo o maquinaria de baldeo.',
                'order' => 6,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-007',
                'name' => 'Limpieza con agua a presión',
                'description' => 'Eliminación de suciedad adherida mediante agua a presión.',
                'order' => 7,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-008',
                'name' => 'Limpieza de plazas y parques',
                'description' => 'Limpieza general de plazas, parques y espacios de encuentro.',
                'order' => 8,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-009',
                'name' => 'Limpieza de zonas peatonales',
                'description' => 'Limpieza de paseos, senderos y áreas destinadas a peatones.',
                'order' => 9,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-010',
                'name' => 'Limpieza de hojas y residuos vegetales',
                'description' => 'Recogida de hojas, ramas pequeñas y residuos provenientes de árboles.',
                'order' => 10,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-011',
                'name' => 'Limpieza de excrementos de animales',
                'description' => 'Retiro y limpieza de excrementos de animales en espacios públicos.',
                'order' => 11,
            ],
            [
                'type' => 'LIM',
                'code' => 'LIM-012',
                'name' => 'Limpieza después de eventos',
                'description' => 'Limpieza de espacios utilizados para ferias, fiestas, mercados o actos públicos.',
                'order' => 12,
            ],

            /*
            |--------------------------------------------------------------------------
            | RES - Gestión de residuos
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'RES',
                'code' => 'RES-001',
                'name' => 'Recolección de residuos ordinarios',
                'description' => 'Recogida de residuos sólidos ordinarios generados en el barrio.',
                'order' => 1,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-002',
                'name' => 'Recolección de residuos voluminosos',
                'description' => 'Retiro de muebles, colchones, electrodomésticos y objetos grandes.',
                'order' => 2,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-003',
                'name' => 'Retiro de escombros',
                'description' => 'Recolección y transporte de restos de construcción y demolición.',
                'order' => 3,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-004',
                'name' => 'Limpieza del entorno de contenedores',
                'description' => 'Retiro de residuos acumulados alrededor de los contenedores.',
                'order' => 4,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-005',
                'name' => 'Lavado de contenedores',
                'description' => 'Limpieza y lavado interior y exterior de contenedores.',
                'order' => 5,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-006',
                'name' => 'Reposición de contenedores',
                'description' => 'Colocación o sustitución de contenedores deteriorados o faltantes.',
                'order' => 6,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-007',
                'name' => 'Retiro de residuos vegetales',
                'description' => 'Recolección de ramas, césped, hojas y restos de poda.',
                'order' => 7,
            ],
            [
                'type' => 'RES',
                'code' => 'RES-008',
                'name' => 'Retiro de residuos peligrosos',
                'description' => 'Identificación, aislamiento y gestión especializada de residuos peligrosos.',
                'order' => 8,
            ],

            /*
            |--------------------------------------------------------------------------
            | REC - Reciclaje y residuos aprovechables
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'REC',
                'code' => 'REC-001',
                'name' => 'Recolección de papel y cartón',
                'description' => 'Recogida separada de papel y cartón reciclable.',
                'order' => 1,
            ],
            [
                'type' => 'REC',
                'code' => 'REC-002',
                'name' => 'Recolección de envases plásticos',
                'description' => 'Recogida separada de envases y materiales plásticos.',
                'order' => 2,
            ],
            [
                'type' => 'REC',
                'code' => 'REC-003',
                'name' => 'Recolección de vidrio',
                'description' => 'Recogida de botellas, frascos y otros residuos de vidrio.',
                'order' => 3,
            ],
            [
                'type' => 'REC',
                'code' => 'REC-004',
                'name' => 'Recolección de metales',
                'description' => 'Recogida de latas, piezas metálicas y otros materiales aprovechables.',
                'order' => 4,
            ],
            [
                'type' => 'REC',
                'code' => 'REC-005',
                'name' => 'Clasificación de materiales reciclables',
                'description' => 'Separación y clasificación de materiales reciclables.',
                'order' => 5,
            ],
            [
                'type' => 'REC',
                'code' => 'REC-006',
                'name' => 'Instalación de puntos de reciclaje',
                'description' => 'Instalación y organización de puntos de recogida selectiva.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | VIA - Mantenimiento de calles y aceras
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'VIA',
                'code' => 'VIA-001',
                'name' => 'Limpieza de calzada',
                'description' => 'Retiro de tierra, piedras, residuos y obstáculos de la calzada.',
                'order' => 1,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-002',
                'name' => 'Limpieza de aceras',
                'description' => 'Limpieza y retiro de residuos de aceras y veredas.',
                'order' => 2,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-003',
                'name' => 'Reparación de baches',
                'description' => 'Reparación de huecos y deformaciones localizadas en pavimentos.',
                'order' => 3,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-004',
                'name' => 'Reparación de aceras',
                'description' => 'Reposición o reparación de losas, baldosas y pavimento peatonal.',
                'order' => 4,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-005',
                'name' => 'Mantenimiento de bordillos',
                'description' => 'Reparación, reposición y limpieza de bordillos.',
                'order' => 5,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-006',
                'name' => 'Limpieza de caminos vecinales',
                'description' => 'Limpieza y conservación de caminos internos y rurales del barrio.',
                'order' => 6,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-007',
                'name' => 'Retiro de obstáculos en vías',
                'description' => 'Retiro de objetos, materiales o elementos que obstruyan el paso.',
                'order' => 7,
            ],
            [
                'type' => 'VIA',
                'code' => 'VIA-008',
                'name' => 'Reparación de rampas peatonales',
                'description' => 'Adecuación y reparación de rampas y accesos peatonales.',
                'order' => 8,
            ],

            /*
            |--------------------------------------------------------------------------
            | VER - Mantenimiento de áreas verdes
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'VER',
                'code' => 'VER-001',
                'name' => 'Corte de césped',
                'description' => 'Corte y mantenimiento de césped en parques y zonas verdes.',
                'order' => 1,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-002',
                'name' => 'Desbroce de áreas verdes',
                'description' => 'Retiro de maleza y vegetación no deseada.',
                'order' => 2,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-003',
                'name' => 'Poda de arbustos',
                'description' => 'Poda y formación de arbustos ornamentales.',
                'order' => 3,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-004',
                'name' => 'Plantación de árboles',
                'description' => 'Plantación y reposición de árboles en espacios verdes.',
                'order' => 4,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-005',
                'name' => 'Plantación de flores',
                'description' => 'Plantación y reposición de flores y plantas ornamentales.',
                'order' => 5,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-006',
                'name' => 'Mantenimiento de parterres',
                'description' => 'Limpieza, deshierbe y conservación de parterres.',
                'order' => 6,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-007',
                'name' => 'Aireado y recuperación de césped',
                'description' => 'Tratamiento de recuperación para zonas de césped deterioradas.',
                'order' => 7,
            ],
            [
                'type' => 'VER',
                'code' => 'VER-008',
                'name' => 'Retiro de ramas caídas',
                'description' => 'Retiro de ramas y restos vegetales caídos en áreas verdes.',
                'order' => 8,
            ],

            /*
            |--------------------------------------------------------------------------
            | RIE - Riego y mantenimiento hidráulico
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'RIE',
                'code' => 'RIE-001',
                'name' => 'Riego manual de áreas verdes',
                'description' => 'Riego manual de jardines, parques y zonas verdes.',
                'order' => 1,
            ],
            [
                'type' => 'RIE',
                'code' => 'RIE-002',
                'name' => 'Riego automatizado',
                'description' => 'Programación y control de sistemas automáticos de riego.',
                'order' => 2,
            ],
            [
                'type' => 'RIE',
                'code' => 'RIE-003',
                'name' => 'Reparación de tuberías de riego',
                'description' => 'Reparación de fugas y daños en tuberías de riego.',
                'order' => 3,
            ],
            [
                'type' => 'RIE',
                'code' => 'RIE-004',
                'name' => 'Reparación de aspersores',
                'description' => 'Revisión y reparación de aspersores y difusores.',
                'order' => 4,
            ],
            [
                'type' => 'RIE',
                'code' => 'RIE-005',
                'name' => 'Limpieza de depósitos de agua',
                'description' => 'Limpieza y mantenimiento de depósitos utilizados para riego.',
                'order' => 5,
            ],
            [
                'type' => 'RIE',
                'code' => 'RIE-006',
                'name' => 'Detección de fugas',
                'description' => 'Inspección y localización de fugas en sistemas hidráulicos.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | DRE - Drenaje y aguas pluviales
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'DRE',
                'code' => 'DRE-001',
                'name' => 'Limpieza de sumideros',
                'description' => 'Retiro de residuos y sedimentos acumulados en sumideros.',
                'order' => 1,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-002',
                'name' => 'Limpieza de rejillas',
                'description' => 'Limpieza y desobstrucción de rejillas de drenaje.',
                'order' => 2,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-003',
                'name' => 'Limpieza de cunetas',
                'description' => 'Retiro de tierra, vegetación y residuos de cunetas.',
                'order' => 3,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-004',
                'name' => 'Limpieza de canaletas',
                'description' => 'Limpieza de canaletas y bajantes de aguas lluvias.',
                'order' => 4,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-005',
                'name' => 'Desobstrucción de drenajes',
                'description' => 'Eliminación de obstrucciones en sistemas de drenaje.',
                'order' => 5,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-006',
                'name' => 'Retiro de sedimentos',
                'description' => 'Extracción de lodo, arena y sedimentos acumulados.',
                'order' => 6,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-007',
                'name' => 'Reparación de rejillas',
                'description' => 'Reposición o reparación de rejillas deterioradas.',
                'order' => 7,
            ],
            [
                'type' => 'DRE',
                'code' => 'DRE-008',
                'name' => 'Atención de inundaciones',
                'description' => 'Intervención para evacuar agua acumulada en espacios públicos.',
                'order' => 8,
            ],

            /*
            |--------------------------------------------------------------------------
            | ALU - Alumbrado público
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'ALU',
                'code' => 'ALU-001',
                'name' => 'Inspección de luminarias',
                'description' => 'Revisión visual y funcional de luminarias públicas.',
                'order' => 1,
            ],
            [
                'type' => 'ALU',
                'code' => 'ALU-002',
                'name' => 'Cambio de bombilla',
                'description' => 'Sustitución de bombillas o fuentes de luz defectuosas.',
                'order' => 2,
            ],
            [
                'type' => 'ALU',
                'code' => 'ALU-003',
                'name' => 'Cambio de luminaria',
                'description' => 'Sustitución completa de luminarias deterioradas.',
                'order' => 3,
            ],
            [
                'type' => 'ALU',
                'code' => 'ALU-004',
                'name' => 'Reparación de postes',
                'description' => 'Reparación y conservación de postes de alumbrado.',
                'order' => 4,
            ],
            [
                'type' => 'ALU',
                'code' => 'ALU-005',
                'name' => 'Revisión de cableado',
                'description' => 'Inspección y reparación de cableado del alumbrado.',
                'order' => 5,
            ],
            [
                'type' => 'ALU',
                'code' => 'ALU-006',
                'name' => 'Limpieza de luminarias',
                'description' => 'Limpieza de polvo, suciedad y residuos en luminarias.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | MOB - Mobiliario urbano
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'MOB',
                'code' => 'MOB-001',
                'name' => 'Limpieza de bancos',
                'description' => 'Limpieza de bancos instalados en espacios públicos.',
                'order' => 1,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-002',
                'name' => 'Reparación de bancos',
                'description' => 'Reparación de estructuras, asientos y respaldos de bancos.',
                'order' => 2,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-003',
                'name' => 'Mantenimiento de papeleras',
                'description' => 'Limpieza, reparación y reposición de papeleras.',
                'order' => 3,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-004',
                'name' => 'Reparación de barandillas',
                'description' => 'Reparación y fijación de barandillas públicas.',
                'order' => 4,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-005',
                'name' => 'Mantenimiento de fuentes',
                'description' => 'Limpieza y conservación de fuentes ornamentales o públicas.',
                'order' => 5,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-006',
                'name' => 'Mantenimiento de bolardos',
                'description' => 'Reparación, limpieza y reposición de bolardos.',
                'order' => 6,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-007',
                'name' => 'Mantenimiento de vallas',
                'description' => 'Reparación y conservación de vallas y cercas públicas.',
                'order' => 7,
            ],
            [
                'type' => 'MOB',
                'code' => 'MOB-008',
                'name' => 'Reposición de mobiliario urbano',
                'description' => 'Sustitución de elementos de mobiliario urbano dañados o faltantes.',
                'order' => 8,
            ],

            /*
            |--------------------------------------------------------------------------
            | JUE - Zonas infantiles
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'JUE',
                'code' => 'JUE-001',
                'name' => 'Limpieza de juegos infantiles',
                'description' => 'Limpieza general de juegos y estructuras infantiles.',
                'order' => 1,
            ],
            [
                'type' => 'JUE',
                'code' => 'JUE-002',
                'name' => 'Inspección de juegos infantiles',
                'description' => 'Revisión de seguridad y estado de los juegos infantiles.',
                'order' => 2,
            ],
            [
                'type' => 'JUE',
                'code' => 'JUE-003',
                'name' => 'Reparación de juegos infantiles',
                'description' => 'Reparación de piezas, uniones y estructuras de juegos.',
                'order' => 3,
            ],
            [
                'type' => 'JUE',
                'code' => 'JUE-004',
                'name' => 'Mantenimiento de superficies amortiguantes',
                'description' => 'Conservación de caucho, arena u otras superficies de seguridad.',
                'order' => 4,
            ],
            [
                'type' => 'JUE',
                'code' => 'JUE-005',
                'name' => 'Reposición de piezas de juegos',
                'description' => 'Sustitución de tornillos, asientos, cadenas, paneles u otras piezas.',
                'order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | DEP - Instalaciones deportivas
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'DEP',
                'code' => 'DEP-001',
                'name' => 'Limpieza de canchas',
                'description' => 'Limpieza de pistas, canchas y superficies deportivas.',
                'order' => 1,
            ],
            [
                'type' => 'DEP',
                'code' => 'DEP-002',
                'name' => 'Mantenimiento de porterías',
                'description' => 'Revisión, reparación y fijación de porterías.',
                'order' => 2,
            ],
            [
                'type' => 'DEP',
                'code' => 'DEP-003',
                'name' => 'Mantenimiento de canastas',
                'description' => 'Revisión y reparación de canastas y tableros deportivos.',
                'order' => 3,
            ],
            [
                'type' => 'DEP',
                'code' => 'DEP-004',
                'name' => 'Reparación de cerramientos deportivos',
                'description' => 'Reparación de mallas, vallas y cerramientos de canchas.',
                'order' => 4,
            ],
            [
                'type' => 'DEP',
                'code' => 'DEP-005',
                'name' => 'Pintura de canchas',
                'description' => 'Pintura y recuperación de líneas y superficies deportivas.',
                'order' => 5,
            ],
            [
                'type' => 'DEP',
                'code' => 'DEP-006',
                'name' => 'Mantenimiento de vestuarios',
                'description' => 'Limpieza y reparación básica de vestuarios y duchas.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | EDI - Edificios comunitarios
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'EDI',
                'code' => 'EDI-001',
                'name' => 'Limpieza de salones comunitarios',
                'description' => 'Limpieza de salones utilizados por la comunidad.',
                'order' => 1,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-002',
                'name' => 'Limpieza de baños comunitarios',
                'description' => 'Limpieza y desinfección de baños comunitarios.',
                'order' => 2,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-003',
                'name' => 'Limpieza de cocinas comunitarias',
                'description' => 'Limpieza de cocinas, fregaderos y superficies de uso comunitario.',
                'order' => 3,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-004',
                'name' => 'Mantenimiento de puertas y ventanas',
                'description' => 'Reparación y ajuste de puertas, cerraduras y ventanas.',
                'order' => 4,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-005',
                'name' => 'Reparación de instalaciones eléctricas',
                'description' => 'Reparaciones básicas de instalaciones eléctricas comunitarias.',
                'order' => 5,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-006',
                'name' => 'Reparación de instalaciones sanitarias',
                'description' => 'Reparación de grifos, tuberías, desagües y sanitarios.',
                'order' => 6,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-007',
                'name' => 'Limpieza de techos y canaletas',
                'description' => 'Limpieza de cubiertas, techos, canaletas y bajantes.',
                'order' => 7,
            ],
            [
                'type' => 'EDI',
                'code' => 'EDI-008',
                'name' => 'Limpieza de fachadas',
                'description' => 'Limpieza exterior de fachadas y paredes comunitarias.',
                'order' => 8,
            ],

            /*
            |--------------------------------------------------------------------------
            | PIN - Pintura y recuperación de superficies
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'PIN',
                'code' => 'PIN-001',
                'name' => 'Pintura de muros públicos',
                'description' => 'Pintura y recuperación de muros ubicados en espacios públicos.',
                'order' => 1,
            ],
            [
                'type' => 'PIN',
                'code' => 'PIN-002',
                'name' => 'Pintura de fachadas comunitarias',
                'description' => 'Pintura de fachadas de edificios y espacios comunitarios.',
                'order' => 2,
            ],
            [
                'type' => 'PIN',
                'code' => 'PIN-003',
                'name' => 'Pintura de bancos',
                'description' => 'Aplicación de pintura y protección en bancos públicos.',
                'order' => 3,
            ],
            [
                'type' => 'PIN',
                'code' => 'PIN-004',
                'name' => 'Pintura de bordillos',
                'description' => 'Pintura y señalización visual de bordillos.',
                'order' => 4,
            ],
            [
                'type' => 'PIN',
                'code' => 'PIN-005',
                'name' => 'Pintura de postes',
                'description' => 'Pintura y protección de postes y estructuras metálicas.',
                'order' => 5,
            ],
            [
                'type' => 'PIN',
                'code' => 'PIN-006',
                'name' => 'Pintura de señalización horizontal',
                'description' => 'Pintura de pasos peatonales, líneas y marcas viales.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | GRA - Grafitis y elementos urbanos
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'GRA',
                'code' => 'GRA-001',
                'name' => 'Retiro de grafitis',
                'description' => 'Eliminación de grafitis en muros, fachadas y mobiliario.',
                'order' => 1,
            ],
            [
                'type' => 'GRA',
                'code' => 'GRA-002',
                'name' => 'Retiro de carteles',
                'description' => 'Eliminación de carteles colocados sin autorización.',
                'order' => 2,
            ],
            [
                'type' => 'GRA',
                'code' => 'GRA-003',
                'name' => 'Retiro de pancartas',
                'description' => 'Retiro de pancartas, lonas y anuncios instalados en espacios públicos.',
                'order' => 3,
            ],
            [
                'type' => 'GRA',
                'code' => 'GRA-004',
                'name' => 'Retiro de adhesivos',
                'description' => 'Eliminación de pegatinas y adhesivos de mobiliario y superficies.',
                'order' => 4,
            ],
            [
                'type' => 'GRA',
                'code' => 'GRA-005',
                'name' => 'Protección antigrafiti',
                'description' => 'Aplicación de productos protectores contra futuras pintadas.',
                'order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | PLA - Control de plagas
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'PLA',
                'code' => 'PLA-001',
                'name' => 'Control de roedores',
                'description' => 'Inspección y control de ratas y ratones.',
                'order' => 1,
            ],
            [
                'type' => 'PLA',
                'code' => 'PLA-002',
                'name' => 'Control de insectos',
                'description' => 'Control de cucarachas, hormigas y otros insectos.',
                'order' => 2,
            ],
            [
                'type' => 'PLA',
                'code' => 'PLA-003',
                'name' => 'Control de mosquitos',
                'description' => 'Tratamiento preventivo y control de mosquitos.',
                'order' => 3,
            ],
            [
                'type' => 'PLA',
                'code' => 'PLA-004',
                'name' => 'Control de avispas',
                'description' => 'Retiro o tratamiento de nidos de avispas.',
                'order' => 4,
            ],
            [
                'type' => 'PLA',
                'code' => 'PLA-005',
                'name' => 'Control de animales invasores',
                'description' => 'Atención y gestión de animales que representen un riesgo.',
                'order' => 5,
            ],
            [
                'type' => 'PLA',
                'code' => 'PLA-006',
                'name' => 'Desinfección de espacios públicos',
                'description' => 'Aplicación de tratamientos de desinfección en espacios comunitarios.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | SAN - Saneamiento y alcantarillado
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'SAN',
                'code' => 'SAN-001',
                'name' => 'Limpieza de alcantarillas',
                'description' => 'Limpieza y retiro de residuos en alcantarillas.',
                'order' => 1,
            ],
            [
                'type' => 'SAN',
                'code' => 'SAN-002',
                'name' => 'Desobstrucción de tuberías',
                'description' => 'Eliminación de obstrucciones en tuberías sanitarias.',
                'order' => 2,
            ],
            [
                'type' => 'SAN',
                'code' => 'SAN-003',
                'name' => 'Inspección de redes sanitarias',
                'description' => 'Inspección del estado de redes y conexiones sanitarias.',
                'order' => 3,
            ],
            [
                'type' => 'SAN',
                'code' => 'SAN-004',
                'name' => 'Reparación de tuberías',
                'description' => 'Reparación de fugas, roturas y daños en tuberías.',
                'order' => 4,
            ],
            [
                'type' => 'SAN',
                'code' => 'SAN-005',
                'name' => 'Limpieza de cámaras de inspección',
                'description' => 'Limpieza y mantenimiento de cámaras de inspección.',
                'order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | SEG - Señalización y seguridad vial
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'SEG',
                'code' => 'SEG-001',
                'name' => 'Instalación de señales verticales',
                'description' => 'Instalación de señales informativas, preventivas o reglamentarias.',
                'order' => 1,
            ],
            [
                'type' => 'SEG',
                'code' => 'SEG-002',
                'name' => 'Reparación de señales verticales',
                'description' => 'Reparación o sustitución de señales verticales deterioradas.',
                'order' => 2,
            ],
            [
                'type' => 'SEG',
                'code' => 'SEG-003',
                'name' => 'Pintura de pasos peatonales',
                'description' => 'Pintura y recuperación de pasos de peatones.',
                'order' => 3,
            ],
            [
                'type' => 'SEG',
                'code' => 'SEG-004',
                'name' => 'Pintura de líneas viales',
                'description' => 'Pintura de líneas, símbolos y marcas viales.',
                'order' => 4,
            ],
            [
                'type' => 'SEG',
                'code' => 'SEG-005',
                'name' => 'Instalación de bolardos',
                'description' => 'Instalación de elementos para proteger zonas peatonales.',
                'order' => 5,
            ],
            [
                'type' => 'SEG',
                'code' => 'SEG-006',
                'name' => 'Instalación de reductores de velocidad',
                'description' => 'Instalación y mantenimiento de elementos para reducir la velocidad.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | ARB - Poda y mantenimiento de árboles
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'ARB',
                'code' => 'ARB-001',
                'name' => 'Poda de formación',
                'description' => 'Poda para orientar el crecimiento y la estructura del árbol.',
                'order' => 1,
            ],
            [
                'type' => 'ARB',
                'code' => 'ARB-002',
                'name' => 'Poda de mantenimiento',
                'description' => 'Poda de ramas secas, dañadas o mal orientadas.',
                'order' => 2,
            ],
            [
                'type' => 'ARB',
                'code' => 'ARB-003',
                'name' => 'Poda de altura',
                'description' => 'Poda realizada sobre árboles de gran altura.',
                'order' => 3,
            ],
            [
                'type' => 'ARB',
                'code' => 'ARB-004',
                'name' => 'Retiro de árbol caído',
                'description' => 'Corte, retiro y transporte de árboles caídos.',
                'order' => 4,
            ],
            [
                'type' => 'ARB',
                'code' => 'ARB-005',
                'name' => 'Retiro de ramas peligrosas',
                'description' => 'Eliminación de ramas que representen un riesgo.',
                'order' => 5,
            ],
            [
                'type' => 'ARB',
                'code' => 'ARB-006',
                'name' => 'Evaluación del estado del árbol',
                'description' => 'Inspección visual y técnica del estado de árboles.',
                'order' => 6,
            ],
            [
                'type' => 'ARB',
                'code' => 'ARB-007',
                'name' => 'Tratamiento fitosanitario',
                'description' => 'Tratamiento contra enfermedades y plagas de árboles.',
                'order' => 7,
            ],

            /*
            |--------------------------------------------------------------------------
            | EME - Atención de emergencias
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'EME',
                'code' => 'EME-001',
                'name' => 'Atención por inundación',
                'description' => 'Atención urgente por acumulación o desbordamiento de agua.',
                'order' => 1,
            ],
            [
                'type' => 'EME',
                'code' => 'EME-002',
                'name' => 'Atención por árbol caído',
                'description' => 'Retiro urgente de árboles que bloqueen vías o representen peligro.',
                'order' => 2,
            ],
            [
                'type' => 'EME',
                'code' => 'EME-003',
                'name' => 'Atención por derrumbe',
                'description' => 'Retiro de tierra, piedras y materiales producto de derrumbes.',
                'order' => 3,
            ],
            [
                'type' => 'EME',
                'code' => 'EME-004',
                'name' => 'Retiro de obstáculos peligrosos',
                'description' => 'Retiro urgente de elementos que representen riesgo para personas o vehículos.',
                'order' => 4,
            ],
            [
                'type' => 'EME',
                'code' => 'EME-005',
                'name' => 'Aislamiento de zona de riesgo',
                'description' => 'Señalización y aislamiento preventivo de áreas peligrosas.',
                'order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | INS - Inspección y diagnóstico
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'INS',
                'code' => 'INS-001',
                'name' => 'Inspección general del barrio',
                'description' => 'Recorrido para identificar necesidades de limpieza y mantenimiento.',
                'order' => 1,
            ],
            [
                'type' => 'INS',
                'code' => 'INS-002',
                'name' => 'Inspección de calles y aceras',
                'description' => 'Evaluación del estado de vías, aceras, bordillos y caminos.',
                'order' => 2,
            ],
            [
                'type' => 'INS',
                'code' => 'INS-003',
                'name' => 'Inspección de áreas verdes',
                'description' => 'Evaluación del estado de parques, jardines, árboles y césped.',
                'order' => 3,
            ],
            [
                'type' => 'INS',
                'code' => 'INS-004',
                'name' => 'Inspección de mobiliario urbano',
                'description' => 'Revisión de bancos, papeleras, vallas, fuentes y otros elementos.',
                'order' => 4,
            ],
            [
                'type' => 'INS',
                'code' => 'INS-005',
                'name' => 'Inspección de alumbrado',
                'description' => 'Identificación de luminarias apagadas, dañadas o inseguras.',
                'order' => 5,
            ],
            [
                'type' => 'INS',
                'code' => 'INS-006',
                'name' => 'Levantamiento fotográfico',
                'description' => 'Registro fotográfico del estado de una infraestructura o espacio.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | OBR - Obras menores
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'OBR',
                'code' => 'OBR-001',
                'name' => 'Reparación de muros',
                'description' => 'Reparación de grietas, desprendimientos y daños en muros.',
                'order' => 1,
            ],
            [
                'type' => 'OBR',
                'code' => 'OBR-002',
                'name' => 'Reparación de bordillos',
                'description' => 'Reposición y reparación de bordillos y separadores.',
                'order' => 2,
            ],
            [
                'type' => 'OBR',
                'code' => 'OBR-003',
                'name' => 'Reparación de pavimento',
                'description' => 'Reparación localizada de superficies pavimentadas.',
                'order' => 3,
            ],
            [
                'type' => 'OBR',
                'code' => 'OBR-004',
                'name' => 'Reparación de escaleras',
                'description' => 'Reparación de peldaños, pasamanos y estructuras de escaleras.',
                'order' => 4,
            ],
            [
                'type' => 'OBR',
                'code' => 'OBR-005',
                'name' => 'Reparación de cubiertas',
                'description' => 'Reparación de techos y cubiertas de edificios comunitarios.',
                'order' => 5,
            ],
            [
                'type' => 'OBR',
                'code' => 'OBR-006',
                'name' => 'Construcción de pequeños elementos',
                'description' => 'Construcción de jardineras, bordes, soportes y elementos menores.',
                'order' => 6,
            ],

            /*
            |--------------------------------------------------------------------------
            | TRA - Transporte y movilización
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'TRA',
                'code' => 'TRA-001',
                'name' => 'Transporte de residuos',
                'description' => 'Transporte de residuos desde el punto de recogida hasta su destino.',
                'order' => 1,
            ],
            [
                'type' => 'TRA',
                'code' => 'TRA-002',
                'name' => 'Transporte de escombros',
                'description' => 'Transporte de escombros y materiales de construcción.',
                'order' => 2,
            ],
            [
                'type' => 'TRA',
                'code' => 'TRA-003',
                'name' => 'Transporte de herramientas',
                'description' => 'Movilización de herramientas y equipos de trabajo.',
                'order' => 3,
            ],
            [
                'type' => 'TRA',
                'code' => 'TRA-004',
                'name' => 'Transporte de materiales',
                'description' => 'Traslado de materiales destinados a trabajos de mantenimiento.',
                'order' => 4,
            ],
            [
                'type' => 'TRA',
                'code' => 'TRA-005',
                'name' => 'Movilización de cuadrilla',
                'description' => 'Transporte de personal y cuadrillas hacia el lugar de trabajo.',
                'order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | LOG - Apoyo logístico comunitario
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'LOG',
                'code' => 'LOG-001',
                'name' => 'Montaje de eventos comunitarios',
                'description' => 'Montaje de espacios, mobiliario y equipos para actividades barriales.',
                'order' => 1,
            ],
            [
                'type' => 'LOG',
                'code' => 'LOG-002',
                'name' => 'Desmontaje de eventos comunitarios',
                'description' => 'Desmontaje y retiro de elementos utilizados en actividades.',
                'order' => 2,
            ],
            [
                'type' => 'LOG',
                'code' => 'LOG-003',
                'name' => 'Instalación de mesas y sillas',
                'description' => 'Colocación y distribución de mesas, sillas y mobiliario temporal.',
                'order' => 3,
            ],
            [
                'type' => 'LOG',
                'code' => 'LOG-004',
                'name' => 'Instalación de vallas temporales',
                'description' => 'Colocación de vallas para organizar o proteger actividades.',
                'order' => 4,
            ],
            [
                'type' => 'LOG',
                'code' => 'LOG-005',
                'name' => 'Limpieza posterior a actividades',
                'description' => 'Limpieza y recuperación del espacio después de una actividad.',
                'order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | CON - Conserjería y control de espacios
            |--------------------------------------------------------------------------
            */
            [
                'type' => 'CON',
                'code' => 'CON-001',
                'name' => 'Apertura de espacios comunitarios',
                'description' => 'Apertura de salones, centros vecinales y otros espacios.',
                'order' => 1,
            ],
            [
                'type' => 'CON',
                'code' => 'CON-002',
                'name' => 'Cierre de espacios comunitarios',
                'description' => 'Cierre y verificación de instalaciones comunitarias.',
                'order' => 2,
            ],
            [
                'type' => 'CON',
                'code' => 'CON-003',
                'name' => 'Control de acceso',
                'description' => 'Control básico de ingreso y salida de usuarios.',
                'order' => 3,
            ],
            [
                'type' => 'CON',
                'code' => 'CON-004',
                'name' => 'Entrega y recepción de llaves',
                'description' => 'Gestión de entrega, recepción y control de llaves.',
                'order' => 4,
            ],
            [
                'type' => 'CON',
                'code' => 'CON-005',
                'name' => 'Supervisión de espacios',
                'description' => 'Supervisión del estado general y uso correcto de instalaciones.',
                'order' => 5,
            ],
        ];

        $now = now();

        $rows = collect($subtypes)
            ->map(function (array $subtype) use ($serviceTypes, $now): array {
                $serviceTypeId = $serviceTypes->get($subtype['type']);

                if (!$serviceTypeId) {
                    throw new \RuntimeException(
                        "No existe el tipo de servicio: {$subtype['type']}"
                    );
                }

                return [
                    'service_type_id' => $serviceTypeId,
                    'code' => $subtype['code'],
                    'name' => $subtype['name'],
                    'description' => $subtype['description'],
                    'sort_order' => $subtype['order'],
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->values()
            ->all();

        DB::table('service_subtypes')->upsert(
            $rows,
            ['code'],
            [
                'service_type_id',
                'name',
                'description',
                'sort_order',
                'active',
                'updated_at',
            ]
        );
    }
}
