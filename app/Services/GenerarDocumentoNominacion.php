<?php

namespace App\Services;

use App\Models\Nomination;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;

class GenerarDocumentoNominacion
{
    public function generarDocumentoCreado(Nomination $nomination)
    {
        // Asegúrate de cargar las relaciones necesarias
        $nomination->load(['nominator', 'candidate', 'verifier', 'approver', 'rejecter']);

        $nombre = sprintf(
            'nomination_%s.pdf',
            $nomination->numero_tramite
        );
        $year = now()->year;

        $path = sprintf(
            'nominations/%s/%s',
            $year,
            $nombre
        );

        $view = match ($nomination->estado) {
            Nomination::ESTADO_PROPUESTA  => 'pdf.nomination_propuesta',
            Nomination::ESTADO_VERIFICADA => 'pdf.nomination_verificada',
            Nomination::ESTADO_APROBADA   => 'pdf.nomination_aprobada',
            Nomination::ESTADO_RECHAZADA  => 'pdf.nomination_rechazada',
            Nomination::ESTADO_EXPIRADA   => 'pdf.nomination_expirada',
            Nomination::ESTADO_ANULADA    => 'pdf.nomination_anulada',
            default => abort(403, 'Estado no permitido para generar el documento'),
        };

        $pdf = Pdf::loadView($view, [
            'nomination'       => $nomination,
            'urlVerificacion'  => route('nominations.index'),
            'path'             => $path,
            'numero_tramite'   => $nomination->numero_tramite,
            'posicion'         => $nomination->role_name,

            'responsable' =>
            trim(($nomination->nominator?->last_name ?? '') . ' ' .
                ($nomination->nominator?->first_name ?? '')) ?: 'No especificado',

            'candidato' =>
            trim(($nomination->candidate?->last_name ?? '') . ' ' .
                ($nomination->candidate?->first_name ?? '')) ?: 'No especificado',

            'verificado_por' =>
            trim(($nomination->verifier?->last_name ?? '') . ' ' .
                ($nomination->verifier?->first_name ?? '')) ?: 'No especificado',

            'aprobado_por' =>
            trim(($nomination->approver?->last_name ?? '') . ' ' .
                ($nomination->approver?->first_name ?? '')) ?: 'No especificado',

            'rechazado_por' =>
            trim(($nomination->rejecter?->last_name ?? '') . ' ' .
                ($nomination->rejecter?->first_name ?? '')) ?: 'No especificado',

            'institucion'      => $nomination->released_by,

            'fechaEmision' =>
            $nomination->fecha_emision?->format('d/m/Y') ?? 'No especificada',

            'fechaInicio' =>
            $nomination->fecha_inicio_vigencia?->format('d/m/Y') ?? 'No especificada',

            'fechaFin' =>
            $nomination->fecha_fin_vigencia?->format('d/m/Y') ?? 'No especificada',

            'observaciones'    => $nomination->observaciones ?? 'Sin observaciones',

            'hash' =>
            $nomination->hash_reference ?? substr(md5($nomination->id), 0, 8),
        ]);



        Storage::put($path, $pdf->output());

        return $path;
    }
}
