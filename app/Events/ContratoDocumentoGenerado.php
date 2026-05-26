<?php

namespace App\Events;

use App\Models\Contrato;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContratoDocumentoGenerado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Contrato $contrato
    ) {}
}
