<?php

namespace App\Services;

class GeoService
{
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
