<?php

namespace App\Services;


use Illuminate\Support\Facades\Log;
use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use Dotenv\Dotenv;
use Exception;

class BlockchainServiceWeb3
{
    protected Web3 $web3;
    protected Contract $contract;
    protected string $privateKey;
    protected string $from;
    protected int $chainId;
    const TIPO_DENUNCIA_CREADA = 1;

    public function __construct()
    {
        // 1. Cargar .env.blockchain (versión mínima)
        $root = base_path();
        $dotenv = Dotenv::createImmutable($root, 'blockchain/.env.blockchain');
        $dotenv->load();

        // 2. Leer variables (versión mínima)
        $rpcUrl   = $_ENV['SEPOLIA_RPC_URL'];
        $this->privateKey = $_ENV['SEPOLIA_PRIVATE_KEY'];
        $this->from = $_ENV['SEPOLIA_FROM_ADDRESS'];        // TODO: DECISIÓN TEMPORAL (jul 2026) — se usa siempre el predio real
        // con el que se validó esta integración (predio 79806, Itchimbia,
        // titular CARRILLO SERRANO JUAN ROBERTO), sin importar lat/lng
        // recibidos. Esto permite probar el flujo completo de notificaciones
        // sin depender aún de calibrar tolerance/mapExtent para coordenadas
        // arbitrarias. Reemplazar por la llamada real de abajo (comentada)
        // cuando se calibre en Septiembre 2026.
        return [
            'numero_predio'      => '79806',
            'clave_catastral'    => '1000405007',
            'nombre_titular'     => 'CARRILLO SERRANO JUAN ROBERTO',
            'identificacion'     => '1703644805',
            'tipo_propietario'   => 'NATURAL',
            'total_propietarios' => 2,
            'parroquia'          => 'ITCHIMBIA',
            'nomenclatura'       => 'N16-37',

            // TODO: correo/celular reales del propietario no existen en el
            // servicio público de GeoQuito. Se resuelven en EnviarNotificacionJob
            // con los datos de contacto del desarrollador (ver DEV_NOTIFICATION_*
            // en .env), no aquí.
            'correo'  => null,
            'celular' => null,
        ];
        $this->chainId = (int) ($_ENV['SEPOLIA_CHAIN_ID'] ?? 11155111);
        $contractAddr = $_ENV['SEPOLIA_CONTRACT_ADDR'];

        if (!$rpcUrl || !$this->privateKey || !$this->from || !$contractAddr) {
            throw new Exception("Variables de blockchain incompletas en .env.blockchain");
        }

        // 3. Inicializar Web3 y contrato (versión mínima)
        $this->web3 = new Web3($rpcUrl);

        $abiPath = base_path('resources/js/artifacts/contracts/AuditoriaEventos.sol/AuditoriaEventos.json'); // ajusta path si usas otro
        $abi = json_decode(file_get_contents($abiPath), true);
        if (isset($abi['abi'])) {
            $abi = $abi['abi'];
        }
        $this->contract = new Contract($this->web3->provider, $abi);
        $this->contract->at($contractAddr);
    }

    public function registrarDenunciaBlockchain(int $denunciaId, string $hash, int $userId): string
    {
        $nonce = $this->getNonce($this->from);

        $dataHash = '0x' . $hash; // bytes32 = 32 bytes = 64 hex chars

        $data = null;
        $this->contract->getData(
            'registrarEvento',
            self::TIPO_DENUNCIA_CREADA,
            $denunciaId,
            $dataHash,
            function ($err, $result) use (&$data) {
                if ($err !== null) {
                    Log::error('Blockchain contract getData error', ['error' => $err->getMessage()]);
                    throw new Exception('Error generando datos de transacción: ' . $err->getMessage());
                }
                $data = $result;
            }
        );

        if (!is_string($data) || $data === '') {
            throw new Exception('No se obtuvo data de contrato para la transacción');
        }

        $tx = new Transaction([
            'nonce'    => '0x' . dechex($nonce),
            'from'     => $this->from,
            'to'       => $_ENV['SEPOLIA_CONTRACT_ADDR'],
            'gas'      => '0x' . dechex(300000),
            'gasPrice' => '0x' . dechex(5 * 10 ** 9),
            'value'    => '0x0',
            'data'     => $data,
            'chainId'  => $this->chainId,
        ]);

        $signed = '0x' . $tx->sign($this->privateKey);

        $txHash = null;
        $this->web3->eth->sendRawTransaction($signed, function ($err, $result) use (&$txHash) {
            if ($err !== null) {
                Log::error("Blockchain TX error", ['error' => $err->getMessage()]);
                throw new Exception("Error enviando transacción: " . $err->getMessage());
            }
            $txHash = $result;
        });

        if (!$txHash) {
            throw new Exception("No se obtuvo hash de transacción");
        }

        return $txHash;
    }
    protected function getNonce(string $address): int
    {
        $nonce = 0;

        $this->web3->eth->getTransactionCount($address, 'pending', function ($err, $result) use (&$nonce) {
            if ($err !== null) {
                throw new Exception("Error obteniendo nonce: " . $err->getMessage());
            }
            $nonce = hexdec($result);
        });

        return $nonce;
    }
}
