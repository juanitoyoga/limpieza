<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ContratoFilter
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        // Eliminamos null, '', false
        $this->filters = array_filter($filters, fn ($v) => $v !== null && $v !== '');
    }

    public function apply(Builder $query): Builder
    {
        return $query
            ->when($this->filters['numero'] ?? null, fn ($q, $v) =>
                $q->where('numero', 'like', "%{$v}%")
            )

            ->when($this->filters['estado'] ?? null, fn ($q, $v) =>
                $q->where('estado', $v)
            )

            ->when($this->filters['barrio'] ?? null, fn ($q, $v) =>
                $q->where('barrio_id', $v)
            )

            // Fechas de inicio
            ->when($this->filters['f_ini_d'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_inicio', '>=', $v)
            )
            ->when($this->filters['f_ini_h'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_inicio', '<=', $v)
            )

            // Verificación
            ->when($this->filters['f_ver_d'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_verificacion', '>=', $v)
            )
            ->when($this->filters['f_ver_h'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_verificacion', '<=', $v)
            )

            // Aprobación
            ->when($this->filters['f_apr_d'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_aprobacion', '>=', $v)
            )
            ->when($this->filters['f_apr_h'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_aprobacion', '<=', $v)
            )

            // Pago
            ->when($this->filters['f_pag_d'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_pago', '>=', $v)
            )
            ->when($this->filters['f_pag_h'] ?? null, fn ($q, $v) =>
                $q->whereDate('fecha_pago', '<=', $v)
            );
    }
}
