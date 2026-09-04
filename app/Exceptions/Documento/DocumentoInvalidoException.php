<?php

namespace App\Exceptions\Documento;

use Exception;

class DocumentoInvalidoException extends Exception
{
    public function __construct(string $mensaje)
    {
        parent::__construct($mensaje);
    }
}
