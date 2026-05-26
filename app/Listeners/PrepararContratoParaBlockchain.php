<?php

namespace App\Listeners;

use App\Events\ContratoDocumentoGenerado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PrepararContratoParaBlockchain
{
    public function handle(ContratoDocumentoGenerado $event): void
    {
        $contrato = $event->contrato;

        if (! $contrato->contrato_path || ! Storage::exists($contrato->contrato_path)) {
            Log::warning('Contrato sin PDF válido para blockchain', [
                'contrato_id' => $contrato->id,
            ]);
            return;
        }

        // 🔐 Hash definitivo del documento
        $documentHash = hash_file(
            'sha256',
            storage_path('app/' . $contrato->contrato_path)
        );

        $contrato->update([
            'document_hash' => $documentHash,
        ]);

        Log::info('Contrato preparado para blockchain', [
            'contrato_id'  => $contrato->id,
            'hash'         => $documentHash,
        ]);
    }
}
