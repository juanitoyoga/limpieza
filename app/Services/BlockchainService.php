<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente HTTP hacia el microservicio blockchain-service (Node.js + ethers.js).
 *
 * Responsabilidad única: hablar con el microservicio. No conoce nada de
 * Denuncia, AuditEvent ni Contratos — esos detalles los maneja el Job
 * que consume este servicio.
 */
class BlockchainService
{
    private string $baseUrl;
    private ?string $internalKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl     = rtrim(config('blockchain.service_url'), '/');
        $this->internalKey = config('blockchain.internal_key');
        $this->timeout     = (int) config('blockchain.timeout', 30);
    }

    /**
     * Publica un evento en el smart contract AuditoriaEventos.
     *
     * @param  int    $tipoEvento   
     * 
     * |--------------------------------------------------------------------------
     * | Mapeo Blockchain - AuditEvent::event_type → tipoEvento (uint8)
     * |--------------------------------------------------------------------------
     * | Fuente original: config/blockchain.php ['tipo_evento_map']
     * | Uso: config('blockchain.tipo_evento_map')['denuncia_pendiente'] // 1
     * |
     * | ESTRUCTURA POR DECENAS (coincide con el contrato Solidity):
     * |
     * | -- Denuncias (1-9) --
     * | 'denuncia_pendiente'           => 1
     * | 'denuncia_verificada'          => 2
     * | 'denuncia_aprobada'            => 3
     * | 'denuncia_rechazada'           => 4
     * | 'denuncia_expirada'            => 5
     * |
     * | -- Multas (10-19) --
     * | 'multa_emitida'                => 11
     * | 'multa_pagada'                 => 12
     * | 'multa_anulada'                => 13
     * | 'multa_impugnada'              => 14
     * |
     * | -- Contratos (20-29) [módulo en desarrollo] --
     * | 'contrato_pendiente'           => 21
     * | 'contrato_verificado'          => 22
     * | 'contrato_aprobado'            => 23
     * | 'contrato_rechazado'           => 24
     * |
     * | -- Nominaciones (30-39) --
     * | 'nominacion_pendiente'         => 31
     * | 'nominacion_verificada'        => 32
     * | 'nominacion_aprobada'          => 33
     * | 'nominacion_rechazada'         => 34
     * |
     * | -- Pagos (40-49) --
     * | 'pago_registrado'              => 41
     * | 'pago_confirmado'              => 42
     * | 'pago_contabilizado'           => 43
     * |
     * | -- Distribucion (50-59) --
     * | 'distribucion_registrada'      => 51
     * | 'distribucion_confirmada'      => 52
     * | 'distribucion_contabilizada'   => 53
     * |
     * | -- Obras (60-69) --
     * | 'obra_propuesta'               => 61
     * | 'obra_aprobada'                => 62
     * | 'obra_rechazada'               => 63
     * |
     * | -- Ejecutorias (70-79) --
     * | 'ejecutoria_emitida'           => 71
     * | 'ejecutoria_pendiente'         => 72
     * | 'ejecutoria_verificada'        => 73
     * | 'ejecutoria_aprobada'          => 74
     * | 'ejecutoria_rechazada'         => 75
     * |
     * | -- Resoluciones (80-89) --
     * | 'resolucion_pendiente'         => 80
     * | 'resolucion_creada'            => 81
     * | 'resolucion_verificada'        => 82
     * | 'resolucion_aprobada'          => 83
     * | 'resolucion_rechazada'         => 84
     * | 'resolucion_anulada'           => 85
     * | 'resolucion_ejecutada'         => 86
     * |
     * | -- Ofertas (90-99) --
     * | 'oferta_creada'                => 90
     * | 'oferta_documento_subido'      => 91
     * | 'oferta_verificada'            => 92
     * | 'oferta_aprobada'              => 93
     * | 'oferta_rechazada'             => 94
     * | 'oferta_rechazada_automatica'  => 95
     * |
     * | -- ContratoServicio (100+) --
     * | 'contrato_servicio_creado'     => 100
     * | 'contrato_servicio_verificado' => 101
     * | 'contrato_servicio_aprobado'   => 102
     * | 'contrato_servicio_rechazado'  => 103
     * | 'contrato_servicio_rescindido' => 104
     * | 'contrato_servicio_liquidado'  => 105
     * |--------------------------------------------------------------------------
 
     * @param  int    $referenciaId  ID del registro (denuncia, contrato, etc.)
     * @param  string $dataHash      SHA-256 hex (con o sin prefijo 0x), 64 chars
     *
     * @return array{txHash:string,blockNumber:int,gasUsado:string,explorerUrl:string}|null
     *         null si la llamada falla (el Job decide si reintentar)
     */
    public function registrar(int $tipoEvento, int $referenciaId, string $dataHash): ?array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/registrar", [
                    'tipoEvento'   => $tipoEvento,
                    'referenciaId' => $referenciaId,
                    'dataHash'     => $this->normalizarHash($dataHash),
                ]);

            if ($response->failed()) {
                Log::error('[BlockchainService] /registrar falló', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                    'tipo'   => $tipoEvento,
                    'ref'    => $referenciaId,
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('[BlockchainService] Excepción en /registrar', [
                'mensaje' => $e->getMessage(),
                'tipo'    => $tipoEvento,
                'ref'     => $referenciaId,
            ]);
            return null;
        }
    }

    /**
     * Consulta los eventos publicados para una referencia.
     *
     * @param  int      $referenciaId
     * @param  int|null $bloqueExacto  Pasar el blockNumber guardado en BD
     *                                 para una consulta instantánea (1 sola
     *                                 llamada RPC en vez de paginar 500 bloques).
     */
    public function consultarEventos(int $referenciaId, ?int $bloqueExacto = null): ?array
    {
        try {
            $query = $bloqueExacto ? ['desde' => $bloqueExacto] : [];

            $response = Http::withHeaders($this->headers())
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/eventos/{$referenciaId}", $query);

            if ($response->failed()) {
                Log::warning('[BlockchainService] /eventos falló', [
                    'status' => $response->status(),
                    'ref'    => $referenciaId,
                ]);
                return null;
            }

            return $response->json('eventos', []);
        } catch (\Throwable $e) {
            Log::error('[BlockchainService] Excepción en /eventos', [
                'mensaje' => $e->getMessage(),
                'ref'     => $referenciaId,
            ]);
            return null;
        }
    }

    /**
     * Verifica que el microservicio esté disponible y conectado a Sepolia.
     */
    public function health(): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/health");
            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::warning('[BlockchainService] /health no disponible: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Resuelve el tipoEvento numérico (1-9) a partir del event_type string
     * de AuditEvent, usando el mapa de config/blockchain.php.
     *
     * @return int|null  null si el event_type no debe publicarse en blockchain
     */
    public function resolverTipoEvento(string $eventType): ?int
    {
        return config("blockchain.tipo_evento_map.{$eventType}");
    }

    private function headers(): array
    {
        return $this->internalKey
            ? ['x-internal-key' => $this->internalKey]
            : [];
    }

    /**
     * Asegura el prefijo 0x y longitud correcta antes de enviar al microservicio.
     */
    private function normalizarHash(string $hash): string
    {
        $clean = str_starts_with($hash, '0x') ? $hash : "0x{$hash}";

        if (strlen($clean) !== 66) {
            throw new \InvalidArgumentException(
                "dataHash debe tener 64 caracteres hex (con 0x = 66). Recibido: " . strlen($clean)
            );
        }

        return $clean;
    }
}
