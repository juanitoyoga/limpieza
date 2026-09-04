<?php

namespace App\Events;

use App\Models\BarrioAtributo;
use App\Models\Denuncia;
use Illuminate\Foundation\Events\Dispatchable;

// app/Events/DenunciaRequiereJustificacion.php
class DenunciaRequiereJustificacion
{
    use Dispatchable;

    public function __construct(
        public Denuncia $denuncia,
        public BarrioAtributo $barrioAtributo,
    ) {}
}
