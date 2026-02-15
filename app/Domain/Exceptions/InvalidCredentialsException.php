<?php

namespace App\Domain\Exceptions;

class InvalidCredentialsException extends DomainException
{
    public function __construct(string $message = 'Invalid credentials')
    {
        parent::__construct($message);
    }
}
