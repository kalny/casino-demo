<?php

namespace Database\Seeders;

use App\Enums\GameType;
use App\Models\Game;
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
                'symbols' => [
                    'A' => 5,
                    'B' => 6,
                    'C' => 7
                ],
                'reel_strip' => ['A', 'A', 'C', 'A', 'B', 'B'],
                'reels_number' => 3,
                'symbols_number' => 3,
                'paylines' => [
                    [[0, 1], [1, 1], [2, 1]]
                ]
            ]
        ]);

        Game::factory()->create([
            'name' => 'Dice Game',
            'type' => GameType::Dice,
        ]);
    }
}
