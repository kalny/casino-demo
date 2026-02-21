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
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Exceptions\InvalidGameTypeException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\GameType;
use App\Domain\Game\OutcomeStatus;
use App\Domain\Game\Dice\DiceSpecificOutcome;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\GameId;
use App\Domain\Game\Slot\SlotSpecificOutcome;
use App\Domain\Game\Slot\ValueObjects\Grid;
use App\Domain\Game\Slot\ValueObjects\WinningPaylines;
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
            gameId: GameId::fromString(1),
            userId: UserId::fromString('id'),
            betAmount: BetAmount::fromInt(100),
            winAmount: WinAmount::fromInt(200),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new DiceSpecificOutcome(
                multiplier: BetMultiplier::fromInt(2),
                roll: DiceNumber::fromInt(4)
            )
        );

        $this->slotGameOutcome = new GameOutcome(
            gameId: GameId::fromString(2),
            userId: UserId::fromString('id2'),
            betAmount: BetAmount::fromInt(100),
            winAmount: WinAmount::fromInt(500),
            outcomeStatus: OutcomeStatus::Win,
            gameSpecificOutcome: new SlotSpecificOutcome(
                multiplier: BetMultiplier::fromInt(2),
                grid: new Grid([]),
                winningPaylines: new WinningPaylines([], BetMultiplier::fromInt(5))
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
