<?php

namespace App\Domain\Exceptions;

class InvalidGameTypeException extends DomainException
{
    public function __construct(string $message = 'Invalid game type')
    {
        parent::__construct($message);
    }
}
