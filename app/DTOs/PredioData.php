<?php

namespace App\DTOs;

final readonly class PredioData
{
    public function __construct(

        /*
        |--------------------------------------------------------------------------
        | Identificación del predio
        |--------------------------------------------------------------------------
        */

        public string $numeroPredio,

        public ?string $claveCatastral = null,

        public ?string $codigoMunicipal = null,

        /*
        |--------------------------------------------------------------------------
        | Ubicación
        |--------------------------------------------------------------------------
        */

        public ?string $direccion = null,

        public ?string $barrio = null,

        public ?string $parroquia = null,

        public ?string $sector = null,

        public ?string $zona = null,

        public ?float $latitud = null,

        public ?float $longitud = null,

        /*
        |--------------------------------------------------------------------------
        | Contribuyente actual
        |--------------------------------------------------------------------------
        */

        public ?string $nombre = null,

        public ?string $identificacion = null,

        public ?string $email = null,

        public ?string $telefono = null,

        /*
        |--------------------------------------------------------------------------
        | Información tributaria
        |--------------------------------------------------------------------------
        */

        public ?float $avaluo = null,

        public ?float $impuesto = null,

        /*
        |--------------------------------------------------------------------------
        | Respuesta original
        |--------------------------------------------------------------------------
        */

        public array $payload = [],
    ) {}

    /**
     * Crear el DTO desde un arreglo.
     */
    public static function fromArray(array $data): self
    {
        return new self(

            numeroPredio: $data['numero_predio'] ?? '',

            claveCatastral: $data['clave_catastral'] ?? null,

            codigoMunicipal: $data['codigo_municipal'] ?? null,

            direccion: $data['direccion'] ?? null,

            barrio: $data['barrio'] ?? null,

            parroquia: $data['parroquia'] ?? null,

            sector: $data['sector'] ?? null,

            zona: $data['zona'] ?? null,

            latitud: isset($data['latitud'])
                ? (float) $data['latitud']
                : null,

            longitud: isset($data['longitud'])
                ? (float) $data['longitud']
                : null,

            nombre: $data['nombre'] ?? null,

            identificacion: $data['identificacion'] ?? null,

            email: $data['email'] ?? null,

            telefono: $data['telefono'] ?? null,

            avaluo: isset($data['avaluo'])
                ? (float) $data['avaluo']
                : null,

            impuesto: isset($data['impuesto'])
                ? (float) $data['impuesto']
                : null,

            payload: $data
        );
    }

    /**
     * Indica si el DTO contiene información válida.
     */
    public function isValid(): bool
    {
        return $this->numeroPredio !== '';
    }

    /**
     * Nombre para mostrar.
     */
    public function displayName(): string
    {
        return $this->nombre ?: 'Sin propietario registrado';
    }

    /**
     * Convertir el DTO a un arreglo.
     */
    public function toArray(): array
    {
        return [

            'numero_predio' => $this->numeroPredio,

            'clave_catastral' => $this->claveCatastral,

            'codigo_municipal' => $this->codigoMunicipal,

            'direccion' => $this->direccion,

            'barrio' => $this->barrio,

            'parroquia' => $this->parroquia,

            'sector' => $this->sector,

            'zona' => $this->zona,

            'latitud' => $this->latitud,

            'longitud' => $this->longitud,

            'nombre' => $this->nombre,

            'identificacion' => $this->identificacion,

            'email' => $this->email,

            'telefono' => $this->telefono,

            'avaluo' => $this->avaluo,

            'impuesto' => $this->impuesto,
        ];
    }
}
