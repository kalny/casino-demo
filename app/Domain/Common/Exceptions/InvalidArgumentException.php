<?php

namespace App\Domain\Common\Exceptions;

class InvalidArgumentException extends DomainException
{
    public function __construct(string $message = 'Invalid argument')
    {
        parent::__construct($message);
    }
}
