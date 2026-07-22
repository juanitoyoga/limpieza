<?php

namespace App\DTOs;

class ContribuyenteData
{
    public function __construct(
        public readonly string $numeroPredio,
        public readonly ?string $claveCatastral = null,
        public readonly ?string $nombre = null,
        public readonly ?string $identificacion = null,
        public readonly ?string $email = null,
        public readonly ?string $telefono = null,
        public readonly ?string $direccion = null,
        public readonly ?string $barrio = null,
        public readonly ?string $parroquia = null,
        public readonly ?float $latitud = null,
        public readonly ?float $longitud = null,
        public readonly ?array $payload = null,
    ) {}

    /**
     * Crear el DTO desde la respuesta de la API del DMQ.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            numeroPredio: $data['numero_predio'] ?? '',
            claveCatastral: $data['clave_catastral'] ?? null,
            nombre: $data['nombre'] ?? null,
            identificacion: $data['identificacion'] ?? null,
            email: $data['email'] ?? null,
            telefono: $data['telefono'] ?? null,
            direccion: $data['direccion'] ?? null,
            barrio: $data['barrio'] ?? null,
            parroquia: $data['parroquia'] ?? null,
            latitud: isset($data['latitud']) ? (float) $data['latitud'] : null,
            longitud: isset($data['longitud']) ? (float) $data['longitud'] : null,
            payload: $data,
        );
    }

    /**
     * Convertir el DTO a un arreglo.
     */
    public function toArray(): array
    {
        return [
            'numero_predio' => $this->numeroPredio,
            'clave_catastral' => $this->claveCatastral,
            'nombre' => $this->nombre,
            'identificacion' => $this->identificacion,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'barrio' => $this->barrio,
            'parroquia' => $this->parroquia,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
        ];
    }

    /**
     * Indica si la información mínima es válida.
     */
    public function isValid(): bool
    {
        return $this->numeroPredio !== '';
    }

    /**
     * Nombre para mostrar en la interfaz.
     */
    public function displayName(): string
    {
        return $this->nombre ?: 'Contribuyente no identificado';
    }
}
