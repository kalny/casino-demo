<?php

namespace Tests\Unit\Domain\User;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\User\UserId;
use Tests\TestCase;

class UserIdTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreatesValidUserIdFromInteger(): void
    {
        $userId = new UserId(1);

        $this->assertSame(1, $userId->getValue());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testEqualsComparesValuesCorrectly(): void
    {
        $userId = new UserId(1);
        $otherUserId = new UserId(1);

        $this->assertTrue($userId->equals($otherUserId));
    }

    public function testCreateUserIdWithNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserId(-1);
    }
}
