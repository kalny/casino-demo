<?php

namespace Database\Seeders;

use App\Domain\Game\GameType;
use App\Infrastructure\Persistence\Eloquent\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::factory()->create([
            'name' => 'Slot Game',
            'type' => GameType::Slot,
            'config' => [
                'reels_number' => 3,
                'symbols_number' => 3,
                'reel_strip' => ['A', 'B', 'C', 'C', 'A', 'A', 'B', 'C', 'A', 'A', 'C'],
                'symbols' => [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'B', 'multiplier' => 6],
                    ['name' => 'C', 'multiplier' => 7]
                ],
                'paylines' => [[[0, 1], [1, 1], [2, 1]]]
            ]
        ]);

        Game::factory()->create([
            'name' => 'Dice Game',
            'type' => GameType::Dice,
            'config' => [
                'multiplier' => 2,
            ]
        ]);
    }
}
