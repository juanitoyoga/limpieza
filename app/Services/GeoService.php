<?php

namespace App\Services;

class GeoService
{
    /**
     * Ray Casting: determina si un punto está dentro de un polígono.
     * @param float $lat  Latitud del punto
     * @param float $lng  Longitud del punto
     * @param array $polygon  Array de [lat, lng]
     */
    public function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $inside = false;
        $count  = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $lng) !== ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) $inside = !$inside;
        }

        return $inside;
    }
}
