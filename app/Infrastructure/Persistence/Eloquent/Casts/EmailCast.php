<?php

namespace App\Infrastructure\Persistence\Eloquent\Casts;

use App\Domain\Common\ValueObjects\Email;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class EmailCast implements CastsAttributes
{
    /**
     * @throws InvalidArgumentException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return Email::fromString($value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (! $value instanceof Email) {
            throw new InvalidArgumentException('The given value is not an Email instance.');
        }

        return $value->getValue();
    }
}
