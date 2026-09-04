<?php

namespace App\Http\Controllers;

use App\Exceptions\Documento\DocumentoAccesoDenegadoException;
use App\Exceptions\Documento\DocumentoInvalidoException;
use App\Exceptions\Documento\DocumentoNoEncontradoException;

class DocumentoController extends Controller
{
    private const DISCOS_PERMITIDOS = [
        'nominations',
        'resoluciones',
        'ofertas',
        'documentos',
        'public',
        'local',
        'contratos_servicios',
        's3',
    ];

    public function ver(string $disco, string $path)
    {
        if (! in_array($disco, self::DISCOS_PERMITIDOS, true)) {
            throw new DocumentoInvalidoException("El disco '{$disco}' no está permitido.");
        }

        $decodedPath = base64_decode($path, true);
        if ($decodedPath === false) {
            throw new DocumentoInvalidoException('La ruta proporcionada no es un base64 válido.');
        }

        $fullPath = storage_path("app/{$disco}/{$decodedPath}");
        $basePath = storage_path("app/{$disco}");

        $realPath = realpath($fullPath);

        if ($realPath === false) {
            throw new DocumentoNoEncontradoException($fullPath);
        }

        if (! str_starts_with($realPath, realpath($basePath))) {
            throw new DocumentoAccesoDenegadoException();
        }

        return response()->file($realPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="documento.pdf"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
