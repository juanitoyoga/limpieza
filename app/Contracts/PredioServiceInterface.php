<?php

// app/Contracts/PredioServiceInterface.php
namespace App\Contracts;

interface PredioServiceInterface
{
    /**
     * @return array{
     *   numero_predio: string,
     *   clave_catastral: string,
     *   nombre_titular: string,
     *   tipo_propietario: string,
     *   total_propietarios: int,
     *   parroquia: string,
     *   nomenclatura: string,
     *   correo: string|null,
     *   celular: string|null,
     * }
     */
    public function resolverPredio(float $latitud, float $longitud): array;
}
