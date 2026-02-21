<?php

namespace Tests\Unit\Domain\Game\Dice;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\DiceGame;
use App\Domain\Game\Dice\RandomDiceNumberGenerator;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Game\Dice\ValueObjects\PlayDiceType;
use App\Domain\Game\GameId;
use App\Domain\User\UserId;
use Tests\TestCase;

class DiceGameTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testPlayDiceWinOver(): void
    {
        $diceGame = new DiceGame(
            gameId: GameId::fromString(1),
            name: 'Dice Game',
            multiplier: BetMultiplier::fromInt(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            chosenNumber: DiceNumber::fromInt(4),
            playDiceType: PlayDiceType::Over,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(5));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertTrue($gameOutcome->isWin());
        $this->assertSame(200, $gameOutcome->winAmount->getValue()); // 100 * 2
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlayDiceWinUnder(): void
    {
        $diceGame = new DiceGame(
            gameId: GameId::fromString(1),
            name: 'Dice Game',
            multiplier: BetMultiplier::fromInt(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            chosenNumber: DiceNumber::fromInt(4),
            playDiceType: PlayDiceType::Under,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(3));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertTrue($gameOutcome->isWin());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlayDiceLossOver(): void
    {
        $diceGame = new DiceGame(
            gameId: GameId::fromString(1),
            name: 'Dice Game',
            multiplier: BetMultiplier::fromInt(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            chosenNumber: DiceNumber::fromInt(4),
            playDiceType: PlayDiceType::Over,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(2));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertFalse($gameOutcome->isWin());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlayDicLossUnder(): void
    {
        $diceGame = new DiceGame(
            gameId: GameId::fromString(1),
            name: 'Dice Game',
            multiplier: BetMultiplier::fromInt(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            chosenNumber: DiceNumber::fromInt(4),
            playDiceType: PlayDiceType::Under,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(6));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertFalse($gameOutcome->isWin());
    }
}
