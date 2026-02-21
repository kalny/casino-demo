<?php

namespace App\Domain\User\Exceptions;

use App\Domain\Common\Exceptions\DomainException;

class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $message = 'User already exists')
    {
        parent::__construct($message);
    }
}
