<?php

namespace App\Services;

class GeoService
{

    private const RADIO_TOLERANCIA_METROS = 50; // TODO: confirmar si Denuncia ya usa otro radio
    private const UMBRAL_MINIMO_APROBACION = 70; // %

    public function calcularPorcentaje(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $distancia = $this->haversine($lat1, $lng1, $lat2, $lng2);
        return max(0, 100 * exp(-$distancia / self::RADIO_TOLERANCIA_METROS));
    }

    public function cumpleUmbral(float $porcentaje): bool
    {
        return $porcentaje >= self::UMBRAL_MINIMO_APROBACION;
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $r * $c;
    }
    /**
     * Ray Casting: determina si un punto está dentro de un polígono.
     * @param float $lat  Latitud del punto
     * @param float $lng  Longitud del punto
     * @param array $polygon  Array de vértices, cada uno ['lat' => float, 'lng' => float]
     */
    public function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        // Soporta también el formato GeoJSON { "coordinates": [[...]] } por si acaso
        if (isset($polygon['coordinates'])) {
            $polygon = $polygon['coordinates'][0];
        }

        $inside = false;
        $count  = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = (float) ($polygon[$i]['lat'] ?? $polygon[$i][1] ?? 0);
            $yi = (float) ($polygon[$i]['lng'] ?? $polygon[$i][0] ?? 0);
            $xj = (float) ($polygon[$j]['lat'] ?? $polygon[$j][1] ?? 0);
            $yj = (float) ($polygon[$j]['lng'] ?? $polygon[$j][0] ?? 0);

            $intersect = (($yi > $lng) !== ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
