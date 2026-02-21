<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class Email
{
    private string $value;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        $normalizedEmailString = mb_strtolower(trim($value));

        if (!filter_var($normalizedEmailString, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Incorrect email format: {$normalizedEmailString}");
        }

        if (empty($normalizedEmailString)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        $this->value = $normalizedEmailString;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
