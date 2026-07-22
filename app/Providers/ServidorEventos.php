<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use App\Events\ContratoDocumentoGenerado;
use App\Listeners\PrepararContratoParaBlockchain;
use App\Events\DenunciaRequiereJustificacion;
use App\Listeners\GenerarNotificacionJustificacion;

class ServidorEventos extends EventServiceProvider
{
    protected $listen = [
        ContratoDocumentoGenerado::class => [
            PrepararContratoParaBlockchain::class,
        ],

        DenunciaRequiereJustificacion::class => [
            GenerarNotificacionJustificacion::class,
        ],
    ];
}
