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
     * @param  int    $tipoEvento    Código numérico por dominio (ver config/blockchain.php):
     *                               1-9 Denuncias · 11-19 Multas · 21-29 Contratos ·
     *                               31-39 Nominaciones · 41-49 Notificaciones
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
