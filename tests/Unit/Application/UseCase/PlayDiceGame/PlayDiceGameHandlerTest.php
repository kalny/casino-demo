<?php

namespace Tests\Unit\Application\UseCase\PlayDiceGame;

use App\Application\Ports\TransactionManager;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameCommand;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameHandler;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\OutcomeStatus;
use App\Domain\Games\Dice\DiceGame;
use App\Domain\Games\Dice\RandomDiceNumberGenerator;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\GameId;
use App\Domain\Games\Repository\GameOutcomeRepository;
use App\Domain\Games\Repository\GameRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use Tests\TestCase;

class PlayDiceGameHandlerTest extends TestCase
{
    private User $user;
    private GameRepository $gameRepository;
    private GameOutcomeRepository $gameOutcomeRepository;
    private UserRepository $userRepository;
    private TransactionManager $transactionManager;
    private RandomDiceNumberGenerator $randomDiceNumberGenerator;

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

        $this->randomDiceNumberGenerator = $this->createMock(RandomDiceNumberGenerator::class);
    }

    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     */
    public function testPlayDiceOverWin(): void
    {
        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->with(1)
            ->willReturn(new DiceGame(
                gameId: new GameId(1),
                name: 'Dice Game',
                multiplier: new BetMultiplier(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(5));

        $handler = new PlayDiceGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rng: $this->randomDiceNumberGenerator
        );

        $outcome = $handler->handle(PlayDiceGameCommand::fromValidated(
            validated: [
                'amount' => 100,
                'params' => [
                    'number' => 4,
                    'bet_type' => 'over'
                ]
            ],
            gameId: 1,
            userId: 1
        ));

        $this->assertSame(OutcomeStatus::Win, $outcome->outcomeStatus);
        $this->assertSame(300, $outcome->winAmount->getValue());
        $this->assertSame(1200, $this->user->getBalance());
    }

    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     */
    public function testPlayDiceUnderWin(): void
    {
        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->with(1)
            ->willReturn(new DiceGame(
                gameId: new GameId(1),
                name: 'Dice Game',
                multiplier: new BetMultiplier(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(5));

        $handler = new PlayDiceGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rng: $this->randomDiceNumberGenerator
        );

        $outcome = $handler->handle(PlayDiceGameCommand::fromValidated(
            validated: [
                'amount' => 100,
                'params' => [
                    'number' => 6,
                    'bet_type' => 'under'
                ]
            ],
            gameId: 1,
            userId: 1
        ));

        $this->assertSame(OutcomeStatus::Win, $outcome->outcomeStatus);
        $this->assertSame(300, $outcome->winAmount->getValue());
        $this->assertSame(1200, $this->user->getBalance());
    }

    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     */
    public function testPlayDiceOverLoss(): void
    {
        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->with(1)
            ->willReturn(new DiceGame(
                gameId: new GameId(1),
                name: 'Dice Game',
                multiplier: new BetMultiplier(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(3));

        $handler = new PlayDiceGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rng: $this->randomDiceNumberGenerator
        );

        $outcome = $handler->handle(PlayDiceGameCommand::fromValidated(
            validated: [
                'amount' => 100,
                'params' => [
                    'number' => 4,
                    'bet_type' => 'over'
                ]
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
     * @throws InsufficientFundsException
     */
    public function testPlayDiceUnderLoss(): void
    {
        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->with(1)
            ->willReturn(new DiceGame(
                gameId: new GameId(1),
                name: 'Dice Game',
                multiplier: new BetMultiplier(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(6));

        $handler = new PlayDiceGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rng: $this->randomDiceNumberGenerator
        );

        $outcome = $handler->handle(PlayDiceGameCommand::fromValidated(
            validated: [
                'amount' => 100,
                'params' => [
                    'number' => 6,
                    'bet_type' => 'under'
                ]
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
    public function testPlayDiceInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $this->user->debit(new BetAmount(1000));

        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->with(1)
            ->willReturn(new DiceGame(
                gameId: new GameId(1),
                name: 'Dice Game',
                multiplier: new BetMultiplier(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(6));

        $handler = new PlayDiceGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rng: $this->randomDiceNumberGenerator
        );

        $handler->handle(PlayDiceGameCommand::fromValidated(
            validated: [
                'amount' => 100,
                'params' => [
                    'number' => 6,
                    'bet_type' => 'under'
                ]
            ],
            gameId: 1,
            userId: 1
        ));
    }

    public function testPlayDiceInvalidPlayerInput(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->with(1)
            ->willReturn(new DiceGame(
                gameId: new GameId(1),
                name: 'Dice Game',
                multiplier: new BetMultiplier(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(new DiceNumber(6));

        $handler = new PlayDiceGameHandler(
            gameRepository: $this->gameRepository,
            gameOutcomeRepository: $this->gameOutcomeRepository,
            userRepository: $this->userRepository,
            transactionManager: $this->transactionManager,
            rng: $this->randomDiceNumberGenerator
        );

        $handler->handle(PlayDiceGameCommand::fromValidated(
            validated: [
                'amount' => 100,
                'params' => [
                    'number' => 6,
                ]
            ],
            gameId: 1,
            userId: 1
        ));
    }
}
