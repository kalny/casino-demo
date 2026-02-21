<?php

namespace App\Domain\User\Exceptions;

use App\Domain\Common\Exceptions\DomainException;

class InvalidCredentialsException extends DomainException
{
    public function __construct(string $message = 'Invalid credentials')
    {
        parent::__construct($message);
    }
}
