<?php

namespace App\Domain\Game\Exceptions;

use App\Domain\Common\Exceptions\DomainException;

class InvalidGameTypeException extends DomainException
{
    public function __construct(string $message = 'Invalid game type')
    {
        parent::__construct($message);
    }
}
