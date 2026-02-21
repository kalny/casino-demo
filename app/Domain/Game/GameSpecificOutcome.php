<?php

namespace App\Domain\Game;

use JsonSerializable;

abstract readonly class GameSpecificOutcome implements JsonSerializable
{
   abstract public function jsonSerialize(): mixed;
}
