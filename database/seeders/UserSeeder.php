<?php

namespace Database\Seeders;

use App\Domain\Common\ValueObjects\Email;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws InvalidArgumentException
     */
    public function run(): void
    {
        User::factory()->create();
    }
}
