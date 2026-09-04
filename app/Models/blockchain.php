<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microservicio blockchain (Node.js + ethers.js)
    |--------------------------------------------------------------------------
    */
    'service_url' => env('BLOCKCHAIN_SERVICE_URL', 'http://localhost:3001'),
    'internal_key' => env('BLOCKCHAIN_INTERNAL_KEY'),
    'timeout' => env('BLOCKCHAIN_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Mapeo: AuditEvent::event_type (string) → tipoEvento (uint8 del contrato)
    |--------------------------------------------------------------------------
    */
    'tipo_evento_map' => [
        // Denuncias
        'denuncia_pendiente'           => 1,
        'denuncia_verificada'          => 2,
        'denuncia_aprobada'            => 3,
        'denuncia_rechazada'           => 4,
        'denuncia_expirada'            => 5,

        // Multas
        'multa_emitida'                => 11,
        'multa_pagada'                 => 12,
        'multa_anulada'                => 13,
        'multa_impugnada'              => 14,

        // Contratos (módulo en desarrollo)
        'contrato_pendiente'           => 21,
        'contrato_verificado'          => 22,
        'contrato_aprobado'            => 23,
        'contrato_rechazado'           => 24,

        // Nominaciones
        'nominacion_pendiente'         => 31,
        'nominacion_verificada'        => 32,
        'nominacion_aprobada'         => 33,
        'nominacion_rechazada'        => 34,

        // Pagos
        'pago_registrado'              => 41,
        'pago_confirmado'              => 42,
        'pago_contabilizado'           => 43,

        // DISTRIBUCION
        'distribucion_registrada'      => 51,
        'distribucion_confirmada'      => 52,
        'distribucion_contabilizada'   => 53,

        // Obras
        'obra_propuesta'               => 61,
        'obra_aprobada'                => 62,
        'obra_rechazada'               => 63,

        // Ejecutorias
        'ejecutoria_emitida'           => 71,
        'ejecutoria_pendiente'         => 72,
        'ejecutoria_verificada'        => 73,
        'ejecutoria_aprobada'          => 74,
        'ejecutoria_rechazada'         => 75,

        // RESOLUCIONES - Decena 80
        'resolucion_pendiente'         => 80,
        'resolucion_creada'            => 81,
        'resolucion_verificada'        => 82,
        'resolucion_aprobada'          => 83,
        'resolucion_rechazada'         => 84,
        'resolucion_anulada'           => 85,
        'resolucion_ejecutada'         => 86,

        // Ofertas (decena 90)
        'oferta_creada'                 => 90,
        'oferta_documento_subido'       => 91,
        'oferta_verificada'             => 92,
        'oferta_aprobada'               => 93,
        'oferta_rechazada'              => 94,
        'oferta_rechazada_automatica'   => 95,

        // ContratoServicio (proveedor) — decena 100
        'contrato_servicio_creado'      => 100,
        'contrato_servicio_verificado'  => 101,
        'contrato_servicio_aprobado'    => 102,
        'contrato_servicio_rechazado'   => 103,
        'contrato_servicio_rescindido'  => 104,
        'contrato_servicio_liquidado'   => 105,

        // Hitos de ContratoServicioDetalle — decena 110
        // Nota: EvidenciaHito NO tiene tx propia; sus hashes (media_uploads.hash_sha256)
        // viajan dentro de los "details" del evento hito_verificado/hito_aprobado.
        'hito_verificado' => 111,
        'hito_aprobado'   => 112,

        // MovimientoServicio — decena 120
        'movimiento_servicio_terminado' => 120,

        // OrdenPago — decena 130
        'orden_pago_generada'   => 130,
        'orden_pago_verificada' => 131,
        'orden_pago_aprobada'   => 132,
        'orden_pago_rechazada'  => 133,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reintentos del Job
    |--------------------------------------------------------------------------
    */
    'job_tries'   => env('BLOCKCHAIN_JOB_TRIES', 3),
    'job_backoff' => env('BLOCKCHAIN_JOB_BACKOFF', 60),

];
