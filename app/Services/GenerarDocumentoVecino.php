<?php

namespace App\Services;

use App\Models\Vecino;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;

class GenerarDocumentoVecino
{
    public function generarRegistroVecino($vecino)
    {
        // Asegúrate de cargar las relaciones necesarias
        $vecino->load(['user', 'barrio', 'intereses']);

        $nombre = sprintf(
            'vecino_%s.pdf',
            $vecino->user->last_name
        );
        $year = now()->year;

        $path = sprintf(
            'vecinos/%s/%s',
            $year,
            $nombre
        );        
        
        $view = 'pdf.vecino';
   
        $pdf = Pdf::loadView($view, [
            'vecino'       => $vecino,
            'path'             => $path,

        
        ]);



        Storage::put($path, $pdf->output());

        return $path;

    }
}