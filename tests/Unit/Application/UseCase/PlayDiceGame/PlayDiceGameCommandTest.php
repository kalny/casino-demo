<?php

namespace Tests\Unit\Application\UseCase\PlayDiceGame;

use App\Application\UseCase\PlayDiceGame\PlayDiceGameCommand;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use Tests\TestCase;

class PlayDiceGameCommandTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreateValidCommandFromValidated(): void
    {
        $validatedArray = [
            'amount' => 100,
            'params' => [
                'number' => 2,
                'bet_type' => 'over',
            ]
        ];

        $command = PlayDiceGameCommand::fromValidated($validatedArray, 'id', 'id');

        $this->assertSame('id', $command->gameId);
        $this->assertSame('id', $command->userId);
        $this->assertSame(100, $command->betAmount);
        $this->assertSame(2, $command->chosenNumber);
        $this->assertSame('over', $command->playDiceType);
    }

    public function testCreateCommandFromValidatedWithIncorrectParamsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $validatedArray = [
            'amount' => 100,
            'params' => [
                'bet_type' => 'over',
            ]
        ];

        PlayDiceGameCommand::fromValidated($validatedArray, 1, 1);
    }

    public function testCreateCommandFromValidatedWithIncorrectParamsBetType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $validatedArray = [
            'amount' => 100,
            'params' => [
                'number' => 2,
            ]
        ];

        PlayDiceGameCommand::fromValidated($validatedArray, 1, 1);
    }
}
