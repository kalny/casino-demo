<?php

namespace Tests\Unit\Application;

use App\Application\GameResolver;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameCommand;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameHandler;
use App\Application\UseCase\PlaySlotGame\PlaySlotGameCommand;
use App\Application\UseCase\PlaySlotGame\PlaySlotGameHandler;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\WinAmount;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\InvalidGameTypeException;
use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\Common\GameType;
use App\Domain\Games\Common\OutcomeStatus;
use App\Domain\Games\Dice\DiceSpecificOutcome;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\GameId;
use App\Domain\Games\Slot\SlotSpecificOutcome;
use App\Domain\Games\Slot\ValueObjects\Grid;
use App\Domain\Games\Slot\ValueObjects\WinningPaylines;
use App\Domain\User\UserId;
use Tests\TestCase;

class GameResolverTest extends TestCase
{
    private PlayDiceGameHandler $playDiceGameHandler;
    private PlaySlotGameHandler $playSlotGameHandler;

    private array $diceValidatedInput;
    private array $slotValidatedInput;

    private GameOutcome $diceGameOutcome;
    private GameOutcome $slotGameOutcome;

    /**
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->diceGameOutcome = new GameOutcome(
            gameId: new GameId(1),
            userId: new UserId('id'),
            betAmount: new BetAmount(100),
            winAmount: new WinAmount(200),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: new BetMultiplier(2),
                roll: new DiceNumber(4)
            )
        );

        $this->slotGameOutcome = new GameOutcome(
            gameId: new GameId(2),
            userId: new UserId('id2'),
            betAmount: new BetAmount(100),
            winAmount: new WinAmount(500),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new SlotSpecificOutcome(
                multiplier: new BetMultiplier(2),
                grid: new Grid([]),
                winningPaylines: new WinningPaylines([], new BetMultiplier(5))
            )
        );

        $this->playDiceGameHandler = $this->createMock(PlayDiceGameHandler::class);
        $this->playSlotGameHandler = $this->createMock(PlaySlotGameHandler::class);

        $this->diceValidatedInput = [
            'amount' => 100,
            'params' => [
                'number' => 4,
                'bet_type' => 'over'
            ]
        ];

        $this->slotValidatedInput = [
            'amount' => 100,
        ];

        $this->playDiceGameHandler
            ->expects($this->any())
            ->method('handle')
            ->with(PlayDiceGameCommand::fromValidated($this->diceValidatedInput, 1, 1))
            ->willReturn($this->diceGameOutcome);

        $this->playSlotGameHandler
            ->expects($this->any())
            ->method('handle')
            ->with(PlaySlotGameCommand::fromValidated($this->slotValidatedInput, 2, 2))
            ->willReturn($this->slotGameOutcome);
    }

    /**
     * @throws InsufficientFundsException
     * @throws InvalidArgumentException
     * @throws InvalidGameTypeException
     */
    public function testResolveGameDice(): void
    {
        $resolver = new GameResolver($this->playDiceGameHandler, $this->playSlotGameHandler);
        $result = $resolver->resolveGame(GameType::Dice, $this->diceValidatedInput, 1, 1);

        $this->assertSame($this->diceGameOutcome, $result);
    }

    /**
     * @throws InsufficientFundsException
     * @throws InvalidArgumentException
     * @throws InvalidGameTypeException
     */
    public function testResolveGameSlot(): void
    {
        $resolver = new GameResolver($this->playDiceGameHandler, $this->playSlotGameHandler);
        $result = $resolver->resolveGame(GameType::Slot, $this->slotValidatedInput, 2, 2);

        $this->assertSame($this->slotGameOutcome, $result);
    }
}
