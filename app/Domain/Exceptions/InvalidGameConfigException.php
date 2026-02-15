<?php

namespace App\Domain\Exceptions;

class InvalidGameConfigException extends DomainException
{
    public function __construct(string $message = 'Invalid game config')
    {
        parent::__construct($message);
    }
}
