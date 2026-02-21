<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Exceptions\InvalidArgumentException;

final readonly class Email
{
    private string $value;

    /**
     * @throws InvalidArgumentException
     */
    private function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Incorrect email format: {$value}");
        }

        if (empty($value)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        $this->value = $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromString(string $value): self
    {
        return new self(mb_strtolower(trim($value)));
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
