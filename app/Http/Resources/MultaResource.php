<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MultaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'codigo_unico'       => $this->codigo_unico,
            'denuncia_id'        => $this->denuncia_id,
            'estado'             => $this->estado,

            'barrio' => $this->whenLoaded('barrio', function () {
                return $this->barrio ? [
                    'id'     => $this->barrio->id,
                    'id_DMQ' => $this->barrio->id_DMQ,
                    'nombre' => $this->barrio->nombre,
                ] : null;
            }),

            'valor_multa' => $this->valor_multa,

            'distribucion' => [
                'barrio'     => ['porcentaje' => $this->porcentaje_barrio,     'valor' => $this->valor_barrio],
                'municipio'  => ['porcentaje' => $this->porcentaje_municipio,  'valor' => $this->valor_municipio],
                'plataforma' => ['porcentaje' => $this->porcentaje_plataforma, 'valor' => $this->valor_plataforma],
            ],

            'pago' => [
                'metodo_pago'      => $this->metodo_pago,
                'referencia_pago'  => $this->referencia_pago,
                'comprobante_pago' => $this->comprobante_pago
                    ? asset('storage/' . $this->comprobante_pago)
                    : null,
                'fecha_pago'       => $this->fecha_pago?->toIso8601String(),
            ],

            'fecha_emision'     => $this->fecha_emision?->toIso8601String(),
            'fecha_vencimiento' => $this->fecha_vencimiento?->toIso8601String(),

            'blockchain' => [
                'tx_hash'          => $this->tx_hash,
                'verified_on_chain' => $this->verified_on_chain,
                'tx_url'           => $this->tx_hash
                    ? "https://sepolia.etherscan.io/tx/{$this->tx_hash}"
                    : null,
            ],

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
