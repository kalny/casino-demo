<?php

namespace Tests\Unit\Infrastructure\Services;

use App\Infrastructure\Services\LaravelPasswordHasher;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaravelPasswordHasherTest extends TestCase
{
    public function testHash(): void
    {
        $hasher = new LaravelPasswordHasher();
        $hashedPassword = $hasher->hash('password');

        $this->assertTrue(Hash::check('password', $hashedPassword));
    }

    public function testCheck(): void
    {
        $hasher = new LaravelPasswordHasher();
        $hashedPassword = $hasher->hash('password');
        $result = $hasher->check('password', $hashedPassword);

        $this->assertSame($result, Hash::check('password', $hashedPassword));
    }
}
