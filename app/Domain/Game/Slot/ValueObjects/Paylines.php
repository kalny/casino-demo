<?php

namespace App\Domain\Game\Slot\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Slot\PaylinesFormatChecker;

final readonly class Paylines
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private array $paylines)
    {
        if (empty($this->paylines)) {
            throw new InvalidArgumentException('Paylines cannot be empty');
        }

        $paylinesFormatChecker = new PaylinesFormatChecker();
        $paylinesFormatChecker->check($this->paylines);
    }

    public function getData(): array
    {
        return $this->paylines;
    }
}
