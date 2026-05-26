<?php

use App\Events\ContratoDocumentoGenerado;
use App\Listeners\PrepararContratoParaBlockchain;

protected $listen = [
    ContratoDocumentoGenerado::class => [
        PrepararContratoParaBlockchain::class,
    ],
];
