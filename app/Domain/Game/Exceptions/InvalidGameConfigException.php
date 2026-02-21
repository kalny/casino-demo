<?php

namespace App\Domain\Game\Exceptions;

use App\Domain\Common\Exceptions\DomainException;

class InvalidGameConfigException extends DomainException
{
    public function __construct(string $message = 'Invalid game config')
    {
        parent::__construct($message);
    }
}
