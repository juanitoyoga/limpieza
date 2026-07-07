<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microservicio blockchain (Node.js + ethers.js)
    |--------------------------------------------------------------------------
    |
    | URL interna del contenedor blockchain-service. En Docker Compose se
    | resuelve por nombre de servicio: http://blockchain-service:3001
    | En desarrollo local sin compose: http://localhost:3001
    |
    */
    'service_url' => env('BLOCKCHAIN_SERVICE_URL', 'http://localhost:3001'),

    'internal_key' => env('BLOCKCHAIN_INTERNAL_KEY'),

    'timeout' => env('BLOCKCHAIN_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Mapeo: AuditEvent::event_type (string) → tipoEvento (uint8 del contrato)
    |--------------------------------------------------------------------------
    |
    | El contrato AuditoriaEventos.sol espera un entero 1-9:
    |   1 = Denuncia creada       6 = Contrato firmado
    |   2 = Denuncia validada     7 = Trabajo ejecutado
    |   3 = Denuncia aprobada     8 = Inspección
    |   4 = Multa emitida         9 = Otro
    |   5 = Pago registrado
    |
    | Solo los eventos listados aquí se publican en blockchain. Cualquier
    | event_type que no esté en este mapa se ignora silenciosamente
    | (auditoría normal en BD, sin transacción on-chain).
    |
    */
    'tipo_evento_map' => [
        // Denuncias
        'denuncia_pendiente'           => 1,
        'denuncia_verificada'          => 2,
        'denuncia_aprobada'            => 3,
        'denuncia_rechazada'           => 4, // Aunque el contrato no tiene un tipo específico para rechazo, lo mapeamos a 4 (multa) para que quede registrado on-chain. Alternativamente podríamos usar 9 (otro).
        'denuncia_expirada'            => 5, // Similar al rechazo, no hay un tipo específico para expiración, pero podemos mapearlo a 5 (pago) o 9 (otro) para que quede registrado. Aquí elegí 5 arbitrariamente.

        // Multas
        'multa_emitida'                => 11,
        'multa_pagada'                 => 12,
        'multa_anulada'                => 13,
        'multa_desactivada'            => 14,

        // Contratos (módulo en desarrollo)
        'contrato_pendiente'           => 21,
        'contrato_verificado'          => 22,
        'contrato_aprobado'            => 23,
        'contrato_rechazado'           => 24,

        // Nominaciones
        'nominacion_pendiente'              => 31,
        'nominacion_verificada'             => 32,
        'nominacion_aprobada'               => 33,
        'nominacion_rechazada'              => 34,

        // pagos
        'pago_registrado'                   => 41,
        'pago_confirmado'                   => 42,
        'pago_contabilizado'                => 43,

        // DISTRIBUCION
        'distribucion_registrada'             => 51,
        'distribucion_confirmada'             => 52,
        'distribucion_contabilizada'          => 53,

        // Obras
        'obra_propuesta'                     => 61,
        'obra_aprobada'                      => 62,
        'obra_rechazada'                     => 63,

        // Ejecutorias
        'ejecutoria_emitida'                 => 71,
        'ejecutoria_pendiente'               => 72,
        'ejecutoria_verificada'              => 73,
        'ejecutoria_aprobada'                => 74,
        'ejecutoria_rechazada'               => 75,


    ],

    /*
    |--------------------------------------------------------------------------
    | Reintentos del Job
    |--------------------------------------------------------------------------
    */
    'job_tries'   => env('BLOCKCHAIN_JOB_TRIES', 3),
    'job_backoff' => env('BLOCKCHAIN_JOB_BACKOFF', 60), // segundos

];
