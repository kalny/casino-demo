<?php

namespace App\Domain\Exceptions;

class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $message = 'User already exists')
    {
        parent::__construct($message);
    }
}
