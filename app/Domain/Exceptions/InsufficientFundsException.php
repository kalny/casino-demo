<?php

namespace App\Domain\Exceptions;

class InsufficientFundsException extends DomainException
{
    public function __construct(string $message = 'Insufficient funds')
    {
        parent::__construct($message);
    }
}
