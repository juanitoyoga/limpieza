<?php

namespace App\Services;

use App\Models\Contrato;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerarDocumentoContrato
{
    public function generarContratoCreado(Contrato $contrato): string
    {
        $contrato->load('barrio');

        $year = now()->year;
        $nombre = "contrato_{$contrato->numero_contrato}.pdf";
        $path = "contratos/{$year}/{$nombre}";

        $view = match ($contrato->estado) {
            Contrato::ESTADO_PENDIENTE   => 'pdf.contrato_pendiente',
            Contrato::ESTADO_VERIFICADO => 'pdf.contrato_verificado',
            Contrato::ESTADO_APROBADO   => 'pdf.contrato_aprobado',
            Contrato::ESTADO_RECHAZADO  => 'pdf.contrato_rechazado',
            default => abort(403, 'Estado no permitido'),
        };

        $pdf = Pdf::loadView($view, [
            'contrato' => $contrato,
            'path'     => $path,
            'hash'     => $contrato->hash_reference
                ?? substr(md5($contrato->id), 0, 8),
        ]);

        Storage::put($path, $pdf->output());

        return $path;
    }
}
