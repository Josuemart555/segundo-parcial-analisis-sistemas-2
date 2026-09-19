<?php

namespace App\Exceptions;

use RuntimeException;

class CitaConflictoException extends RuntimeException
{
    public function __construct(string $message = 'El doctor ya tiene una cita activa en ese horario.')
    {
        parent::__construct($message);
    }
}
