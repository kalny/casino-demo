<?php

namespace Tests\Unit\Domain\Game\Dice\ValueObjects;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Game\Dice\ValueObjects\PlayDiceType;
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
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            chosenNumber: DiceNumber::fromInt(1),
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
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            chosenNumber: DiceNumber::fromInt(1),
            playDiceType: PlayDiceType::Under,
        );

        $this->assertFalse($playDiceInput->isOver());
    }
}
