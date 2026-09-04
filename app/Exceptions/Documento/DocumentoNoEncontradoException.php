<?php

namespace App\Exceptions\Documento;

use Exception;

class DocumentoNoEncontradoException extends Exception
{
    public function __construct(
        public readonly string $rutaEsperada,
    ) {
        parent::__construct("El archivo físico no existe en la ruta esperada: {$rutaEsperada}");
    }
}
