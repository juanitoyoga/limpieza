<?php

namespace App\Services;

use App\Exceptions\PredioNoResueltoException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DmqPredioService
{
    private const ENDPOINT = 'https://geoquito.quito.gob.ec/server/rest/services/web_reference_dmc/catastro_dmc_view/MapServer/identify';

    public function resolverPredio(float $latitud, float $longitud): array
    {

        // TODO: DECISIÓN TEMPORAL (jul 2026) — se usa siempre el predio real
        // con el que se validó esta integración (predio 79806, Itchimbia,
        // titular CARRILLO SERRANO JUAN ROBERTO), sin importar lat/lng
        // recibidos. Esto permite probar el flujo completo de notificaciones
        // sin depender aún de calibrar tolerance/mapExtent para coordenadas
        // arbitrarias. Reemplazar por la llamada real de abajo (comentada)
        // cuando se calibre en Septiembre 2026.
        return [
            'numero_predio'      => '79806',
            'clave_catastral'    => '1000405007',
            'nombre_titular'     => 'CARRILLO SERRANO JUAN ROBERTO',
            'identificacion'     => '1703644805',
            'tipo_propietario'   => 'NATURAL',
            'total_propietarios' => 2,
            'parroquia'          => 'ITCHIMBIA',
            'nomenclatura'       => 'N16-37',

            // TODO: correo/celular reales del propietario no existen en el
            // servicio público de GeoQuito. Se resuelven en EnviarNotificacionJob
            // con los datos de contacto del desarrollador (ver DEV_NOTIFICATION_*
            // en .env), no aquí.
            'correo'  => 'jrcscarrillo@gmail.com',
            'celular' => '+16084734446',
        ];

        $response = Http::timeout(10)->get(self::ENDPOINT, [
            'f'              => 'json',
            'geometry'       => "{$longitud},{$latitud}", // ArcGIS espera x,y = lng,lat
            'geometryType'   => 'esriGeometryPoint',
            'sr'             => 4326,
            'tolerance'      => 5, // TODO: calibrar en Sept 2026 con dato real de precisión GPS de la app Android
            'mapExtent'      => $this->calcularExtent($latitud, $longitud),
            'imageDisplay'   => '800,600,96',
            'layers'         => 'all:14',
            'returnGeometry' => false,
        ]);

        if (!$response->successful()) {
            throw new PredioNoResueltoException('Servicio GeoQuito no disponible.');
        }

        $atributos = $response->json('results.0.attributes');

        if (!$atributos) {
            throw new PredioNoResueltoException('No se encontró predio para esa ubicación.');
        }

        return [
            'numero_predio'      => (string) $atributos['predio'],
            'clave_catastral'    => $atributos['clavcatastral'],
            'nombre_titular'     => $atributos['denominacion'],
            'identificacion'     => $atributos['identificacion'],
            'tipo_propietario'   => $atributos['tippropietario'],
            'total_propietarios' => (int) $atributos['numtotalpropietarios'],
            'parroquia'          => $atributos['parroquia'],
            'nomenclatura'       => $atributos['nomenclatura'],

            // TODO: GeoQuito (capa 14, catastro_dmc_view) NO expone correo ni celular
            // del propietario. Estos dos valores son simulados mientras se gestiona
            // el trámite de acceso al servicio confidencial real con el Municipio
            // (pendiente: Septiembre 2026, visita presencial a IT/Catastro).
            'correo'  => "contribuyente.{$atributos['predio']}@example.com",
            'celular' => '0999123456',
        ];
    }

    /**
     * ArcGIS identify requiere un mapExtent para calcular tolerancia en píxeles.
     * TODO: buffer=0.005° (~500m) puesto a ojo; recalibrar con dato real de
     * precisión GPS reportada por el dispositivo Android al crear la denuncia.
     */
    private function calcularExtent(float $lat, float $lng, float $buffer = 0.005): string
    {
        return implode(',', [
            $lng - $buffer,
            $lat - $buffer,
            $lng + $buffer,
            $lat + $buffer,
        ]);
    }
}
