<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testAdjustBalanceAdd(): void
    {
        $user = User::factory()->create([
            'balance' => 100
        ]);

        $user->adjustBalance(10);
        $user->save();

        $this->assertDatabaseHas('users', [
            'balance' => 110
        ]);
    }

    public function testAdjustBalanceSub(): void
    {
        $user = User::factory()->create([
            'balance' => 100
        ]);

        $user->adjustBalance(-10);
        $user->save();

        $this->assertDatabaseHas('users', [
            'balance' => 90
        ]);
    }
}
