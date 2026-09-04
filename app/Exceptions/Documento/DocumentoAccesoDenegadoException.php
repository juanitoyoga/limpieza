<?php

namespace App\Exceptions\Documento;

use Exception;

class DocumentoAccesoDenegadoException extends Exception
{
    public function __construct(string $mensaje = 'La ruta solicitada está fuera del directorio permitido.')
    {
        parent::__construct($mensaje);
    }
}
