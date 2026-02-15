<?php

namespace Tests\Unit\Application\UseCase\PlaySlotGame;

use App\Application\Ports\TransactionManager;
use App\Application\UseCase\PlaySlotGame\PlaySlotGameCommand;
use App\Application\UseCase\PlaySlotGame\PlaySlotGameHandler;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\OutcomeStatus;
use App\Domain\Games\GameId;
use App\Domain\Games\Repository\GameOutcomeRepository;
use App\Domain\Games\Repository\GameRepository;
use App\Domain\Games\Services\RandomGridGenerator;
use App\Domain\Games\Slot\SlotGame;
use App\Domain\Games\Slot\ValueObjects\Grid;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\Paylines;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;
use App\Domain\Games\Slot\ValueObjects\SymbolsCollection;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use Tests\TestCase;

class PlaySlotGameHandlerTest extends TestCase
{
    private User $user;
    private GameRepository $gameRepository;
    private GameOutcomeRepository $gameOutcomeRepository;
    private UserRepository $userRepository;
    private TransactionManager $transactionManager;
    private RandomGridGenerator $randomGridGenerator;

    /**
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User(
            id: new UserId(1),
            name: 'Test User',
            email: new Email('test@example.com'),
            password: 'test',
            balance: 1000
        );

        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->gameOutcomeRepository = $this->createMock(GameOutcomeRepository::class);
        $this->gameOutcomeRepository
            ->expects($this->any())
            ->method('save');

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userRepository
            ->expects($this->any())
            ->method('getById')
            ->with(1)
            ->willReturn($this->user);

        $this->userRepository
            ->expects($this->any())
            ->method('save')
            ->with($this->user);

        $this->transactionManager = new LaravelTransactionManager();

        $this->randomGridGenerator = $this->createMock(RandomGridGenerator::class);

        $this->randomGridGenerator
            ->expects($this->any())
            ->method('nextGrid')
            ->willReturn(new Grid([
                [
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'C', 'multiplier' => 7],
                ],
                [
                    ['name' => 'D', 'multiplier' => 8],
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'E', 'multiplier' => 9],
                ],
                [
                    ['name' => 'C', 'multiplier' => 7],
                    ['name' => 'A', 'multiplier' => 5],
                    ['name' => 'A', 'multiplier' => 5],
                ]
            ]));
    }

    /**
     * @throws InsufficientFundsException
     * @throws InvalidArgumentException
     */
    public function testPlaySlotWin(): void
    {
        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
            ['name' => 'E', 'multiplier' => 9],
        ]);

        $paylines = new Paylines([
            [[0, 1], [1, 1], [2, 1]],
            [[0, 0], [1, 1], [2, 2]],
        ]);

        $reelStrip = new ReelStrip(
            ['A', 'A', 'C', 'B', 'D', 'A', 'E', 'E', 'C', 'A', 'A'],
            $symbolsCollection
        );

        $this->gameRepository
            ->expects($this->any())
            ->method('getSlotGameById')
            ->with(1)
            ->willReturn(new SlotGame(
                gameId: new GameId(1),
                name: 'Slot Game',
                reelsNumber: new GridInt(3),
                symbolsNumber: new GridInt(3),
                reelStrip: $reelStrip,
                paylines: $paylines
            ));

        $handler = new PlaySlotGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rgg: $this->randomGridGenerator
        );

        $outcome = $handler->handle(PlaySlotGameCommand::fromValidated(
            validated: [
                'amount' => 100
            ],
            gameId: 1,
            userId: 1
        ));

        $this->assertSame(OutcomeStatus::Win, $outcome->outcomeStatus);
        $this->assertSame(1000, $outcome->winAmount->getValue());
        $this->assertSame(1900, $this->user->getBalance());
    }

    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     */
    public function testPlaySlotLoss(): void
    {
        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
            ['name' => 'E', 'multiplier' => 9],
        ]);

        $paylines = new Paylines([
            [[0, 0], [1, 0], [2, 0]],
            [[0, 2], [1, 2], [2, 2]],
        ]);

        $reelStrip = new ReelStrip(
            ['A', 'A', 'C', 'B', 'D', 'A', 'E', 'E', 'C', 'A', 'A'],
            $symbolsCollection
        );

        $this->gameRepository
            ->expects($this->any())
            ->method('getSlotGameById')
            ->with(1)
            ->willReturn(new SlotGame(
                gameId: new GameId(1),
                name: 'Slot Game',
                reelsNumber: new GridInt(3),
                symbolsNumber: new GridInt(3),
                reelStrip: $reelStrip,
                paylines: $paylines
            ));

        $handler = new PlaySlotGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rgg: $this->randomGridGenerator
        );

        $outcome = $handler->handle(PlaySlotGameCommand::fromValidated(
            validated: [
                'amount' => 100
            ],
            gameId: 1,
            userId: 1
        ));

        $this->assertSame(OutcomeStatus::Loss, $outcome->outcomeStatus);
        $this->assertSame(0, $outcome->winAmount->getValue());
        $this->assertSame(900, $this->user->getBalance());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testPlaySlotInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $this->user->debit(new BetAmount(1000));

        $symbolsCollection = new SymbolsCollection([
            ['name' => 'A', 'multiplier' => 5],
            ['name' => 'B', 'multiplier' => 6],
            ['name' => 'C', 'multiplier' => 7],
            ['name' => 'D', 'multiplier' => 8],
            ['name' => 'E', 'multiplier' => 9],
        ]);

        $paylines = new Paylines([
            [[0, 0], [1, 0], [2, 0]],
            [[0, 2], [1, 2], [2, 2]],
        ]);

        $reelStrip = new ReelStrip(
            ['A', 'A', 'C', 'B', 'D', 'A', 'E', 'E', 'C', 'A', 'A'],
            $symbolsCollection
        );

        $this->gameRepository
            ->expects($this->any())
            ->method('getSlotGameById')
            ->with(1)
            ->willReturn(new SlotGame(
                gameId: new GameId(1),
                name: 'Slot Game',
                reelsNumber: new GridInt(3),
                symbolsNumber: new GridInt(3),
                reelStrip: $reelStrip,
                paylines: $paylines
            ));

        $handler = new PlaySlotGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rgg: $this->randomGridGenerator
        );

        $handler->handle(PlaySlotGameCommand::fromValidated(
            validated: [
                'amount' => 100
            ],
            gameId: 1,
            userId: 1
        ));
    }
}
