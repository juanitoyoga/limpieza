<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

use Web3\Web3;

use Web3\Providers\HttpProvider;

use Web3\RequestManagers\HttpRequestManager;

use App\Http\Controllers\Controller;

class Web3Controller extends Controller
{
    private $web3;

    public function __construct()
    {
        $provider = new HttpProvider(
            new HttpRequestManager(config('web3.rpc_url'), config('web3.timeout'))
        );
        $this->web3 = new Web3($provider);
    }

    /**
     * Obtener el último número de bloque
     */
    public function blockNumber(): JsonResponse
    {
        try {
            $this->web3->eth->blockNumber(function ($err, $block) use (&$result) {
                if ($err) {
                    throw new \Exception($err->getMessage());
                }
                $result = [
                    'success' => true,
                    'block_number' => $block->toString(),
                    'block_number_hex' => '0x' . $block->toHex()
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener balance de una dirección
     */
    public function getBalance($address): JsonResponse
    {
        try {
            // Validar formato de dirección
            if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formato de dirección inválido'
                ], 400);
            }

            $this->web3->eth->getBalance($address, function ($err, $balance) use (&$result) {
                if ($err) {
                    throw new \Exception($err->getMessage());
                }
                $result = [
                    'success' => true,
                    'address' => $address,
                    'balance_wei' => $balance->toString(),
                    'balance_ether' => $this->weiToEther($balance->toString())
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de transacción
     */
    public function getTransaction($hash): JsonResponse
    {
        try {
            if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $hash)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formato de hash inválido'
                ], 400);
            }

            $this->web3->eth->getTransaction($hash, function ($err, $transaction) use (&$result) {
                if ($err) {
                    throw new \Exception($err->getMessage());
                }
                $result = [
                    'success' => true,
                    'transaction' => $transaction
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener precio del gas
     */
    public function getGasPrice(): JsonResponse
    {
        try {
            $this->web3->eth->gasPrice(function ($err, $gasPrice) use (&$result) {
                if ($err) {
                    throw new \Exception($err->getMessage());
                }
                $result = [
                    'success' => true,
                    'gas_price_wei' => $gasPrice->toString(),
                    'gas_price_gwei' => $this->weiToGwei($gasPrice->toString())
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convertir Wei a Ether
     */
    private function weiToEther($wei): string
    {
        return bcdiv($wei, '1000000000000000000', 18);
    }

    /**
     * Convertir Wei a Gwei
     */
    private function weiToGwei($wei): string
    {
        return bcdiv($wei, '1000000000', 9);
    }
}