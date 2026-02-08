<?php

namespace Database\Factories;

use App\Models\Bet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bet>
 */
class BetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomNumber(4, 0),
            'bet_data' => [],
        ];
    }
}
