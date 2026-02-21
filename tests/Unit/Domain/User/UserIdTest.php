<?php

namespace Tests\Unit\Domain\User;

use App\Domain\User\UserId;
use Tests\TestCase;

class UserIdTest extends TestCase
{
    public function testCreatesValidUserIdFromInteger(): void
    {
        $userId = UserId::fromString('id');

        $this->assertSame('id', $userId->getValue());
    }

    public function testEqualsComparesValuesCorrectly(): void
    {
        $userId = UserId::fromString('id');
        $otherUserId = UserId::fromString('id');

        $this->assertTrue($userId->equals($otherUserId));
    }
}
