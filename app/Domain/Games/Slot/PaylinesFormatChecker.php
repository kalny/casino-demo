<?php

namespace App\Domain\Games\Slot;

use App\Domain\Exceptions\InvalidArgumentException;

class PaylinesFormatChecker
{
    /**
     * @throws InvalidArgumentException
     */
    public function check(array $paylines): void
    {
        foreach ($paylines as $payline) {
            if (!is_array($payline)) {
                throw new InvalidArgumentException('Payline must be an array');
            }

            foreach ($payline as $item) {
                if (!is_array($item)) {
                    throw new InvalidArgumentException('Payline item must be an array');
                }

                if (!isset($item[0]) || !is_int($item[0])) {
                    throw new InvalidArgumentException('Wrong payline item format');
                }

                if (!isset($item[1]) || !is_int($item[1])) {
                    throw new InvalidArgumentException('Wrong payline item format');
                }
            }
        }
    }
}
