<?php

namespace Database\Factories;

use App\Domain\Game\GameType;
use App\Infrastructure\Persistence\Eloquent\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(GameType::cases()),
            'config' => []
        ];
    }
}
