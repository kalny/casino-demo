<?php

namespace Tests\Unit\Domain\User;

use App\Domain\User\UserId;
use Tests\TestCase;

class UserIdTest extends TestCase
{
    public function testCreatesValidUserIdFromInteger(): void
    {
        $userId = new UserId('id');

        $this->assertSame('id', $userId->getValue());
    }

    public function testEqualsComparesValuesCorrectly(): void
    {
        $userId = new UserId('id');
        $otherUserId = new UserId('id');

        $this->assertTrue($userId->equals($otherUserId));
    }
}
