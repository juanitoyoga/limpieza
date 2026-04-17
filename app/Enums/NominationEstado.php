<?php

namespace App\Enums;

enum NominationEstado: string
{
    case PROPUESTA  = 'propuesta';
    case VERIFICADA = 'verificada';
    case APROBADA   = 'aprobada';
    case RECHAZADA  = 'rechazada';
    case ANULADA    = 'anulada';
    case EXPIRADA   = 'expirada';

    public function color(): string
    {
        return match ($this) {
            self::PROPUESTA  => 'bg-blue-600',
            self::VERIFICADA => 'bg-indigo-600',
            self::APROBADA   => 'bg-green-600',
            self::RECHAZADA  => 'bg-red-600',
            self::ANULADA    => 'bg-gray-600',
            self::EXPIRADA   => 'bg-orange-600',
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
