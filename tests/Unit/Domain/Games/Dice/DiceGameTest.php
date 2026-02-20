<?php

namespace Tests\Unit\Domain\Games\Dice;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Dice\DiceGame;
use App\Domain\Games\Dice\RandomDiceNumberGenerator;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Games\Dice\ValueObjects\PlayDiceType;
use App\Domain\Games\GameId;
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
            gameId: new GameId(1),
            name: 'Dice Game',
            multiplier: new BetMultiplier(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: new UserId(1),
            betAmount: new BetAmount(100),
            chosenNumber: new DiceNumber(4),
            playDiceType: PlayDiceType::Over,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(5));

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
            gameId: new GameId(1),
            name: 'Dice Game',
            multiplier: new BetMultiplier(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: new UserId(1),
            betAmount: new BetAmount(100),
            chosenNumber: new DiceNumber(4),
            playDiceType: PlayDiceType::Under,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(3));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertTrue($gameOutcome->isWin());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlayDiceLossOver(): void
    {
        $diceGame = new DiceGame(
            gameId: new GameId(1),
            name: 'Dice Game',
            multiplier: new BetMultiplier(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: new UserId(1),
            betAmount: new BetAmount(100),
            chosenNumber: new DiceNumber(4),
            playDiceType: PlayDiceType::Over,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(2));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertFalse($gameOutcome->isWin());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlayDicLossUnder(): void
    {
        $diceGame = new DiceGame(
            gameId: new GameId(1),
            name: 'Dice Game',
            multiplier: new BetMultiplier(2),
        );

        $playDiceInput = new PlayDiceInput(
            userId: new UserId(1),
            betAmount: new BetAmount(100),
            chosenNumber: new DiceNumber(4),
            playDiceType: PlayDiceType::Under,
        );

        $randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
        $randomDiceNumberGenerator
            ->expects($this->once())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(6));

        $gameOutcome = $diceGame->playDice($playDiceInput, $randomDiceNumberGenerator);

        $this->assertFalse($gameOutcome->isWin());
    }
}
