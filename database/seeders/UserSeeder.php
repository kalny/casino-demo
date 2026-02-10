<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Anton Kalnyi',
            'email' => 'kalnyanton@gmail.com',
            'balance' => 1000000
        ]);
    }
}
