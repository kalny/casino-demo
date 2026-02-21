<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\Bet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Bet>
 */
class BetFactory extends Factory
{
    protected $model = Bet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'amount' => $this->faker->randomNumber(4, 0),
            'bet_data' => [],
        ];
    }
}
