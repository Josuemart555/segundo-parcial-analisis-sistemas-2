<?php

namespace App\Exceptions;

use RuntimeException;

class TransicionEstadoInvalidaException extends RuntimeException
{
    public function __construct(string $message = 'La transición de estado solicitada no es válida.')
    {
        parent::__construct($message);
    }
}
