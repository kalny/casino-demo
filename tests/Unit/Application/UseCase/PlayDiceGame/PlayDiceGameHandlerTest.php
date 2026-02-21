<?php

namespace Tests\Unit\Application\UseCase\PlayDiceGame;

use App\Application\Ports\TransactionManager;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameCommand;
use App\Application\UseCase\PlayDiceGame\PlayDiceGameHandler;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Domain\Common\ValueObjects\Email;
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\OutcomeStatus;
use App\Domain\Game\Dice\DiceGame;
use App\Domain\Game\Dice\RandomDiceNumberGenerator;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameOutcomeRepository;
use App\Domain\Game\Repository\GameRepository;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User(
            id: UserId::fromString('id'),
            name: 'Test User',
            email: Email::fromString('test@example.com'),
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
            ->willReturn(new DiceGame(
                gameId: GameId::fromString('id'),
                name: 'Dice Game',
                multiplier: BetMultiplier::fromInt(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(5));

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
            ->willReturn(new DiceGame(
                gameId: GameId::fromString('id'),
                name: 'Dice Game',
                multiplier: BetMultiplier::fromInt(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(5));

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
            ->willReturn(new DiceGame(
                gameId: GameId::fromString('id'),
                name: 'Dice Game',
                multiplier: BetMultiplier::fromInt(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(3));

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
            ->willReturn(new DiceGame(
                gameId: GameId::fromString('id'),
                name: 'Dice Game',
                multiplier: BetMultiplier::fromInt(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(6));

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

        $this->user->debit(BetAmount::fromInt(1000));

        $this->gameRepository
            ->expects($this->any())
            ->method('getDiceGameById')
            ->willReturn(new DiceGame(
                gameId: GameId::fromString('id'),
                name: 'Dice Game',
                multiplier: BetMultiplier::fromInt(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(6));

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
            ->willReturn(new DiceGame(
                gameId: GameId::fromString('id'),
                name: 'Dice Game',
                multiplier: BetMultiplier::fromInt(3)
            ));

        $this->randomDiceNumberGenerator
            ->expects($this->any())
            ->method('nextNumber')
            ->willReturn(DiceNumber::fromInt(6));

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
