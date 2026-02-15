<?php

namespace App\Domain\Games\Common;

use JsonSerializable;

abstract readonly class GameSpecificOutcome implements JsonSerializable
{
   abstract public function jsonSerialize(): mixed;
}
