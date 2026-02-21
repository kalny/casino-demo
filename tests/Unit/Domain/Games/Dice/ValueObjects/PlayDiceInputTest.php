<?php

namespace Tests\Unit\Domain\Games\Dice\ValueObjects;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Games\Dice\ValueObjects\PlayDiceType;
use App\Domain\User\UserId;
use Tests\TestCase;

class PlayDiceInputTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testCreatePlayDiceInputWithOverType(): void
    {
        $playDiceInput = new PlayDiceInput(
            userId: new UserId('id'),
            betAmount: new BetAmount(100),
            chosenNumber: new DiceNumber(1),
            playDiceType: PlayDiceType::Over,
        );

        $this->assertTrue($playDiceInput->isOver());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCreatePlayDiceInputWithUnderType(): void
    {
        $playDiceInput = new PlayDiceInput(
            userId: new UserId('id'),
            betAmount: new BetAmount(100),
            chosenNumber: new DiceNumber(1),
            playDiceType: PlayDiceType::Under,
        );

        $this->assertFalse($playDiceInput->isOver());
    }
}
