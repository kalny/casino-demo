<?php

namespace App\Application\UseCase\PlayDiceGame;

use App\Application\Ports\TransactionManager;
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\Dice\RandomDiceNumberGenerator;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Game\Dice\ValueObjects\PlayDiceType;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameOutcomeRepository;
use App\Domain\Game\Repository\GameRepository;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UserId;

class PlayDiceGameHandler
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly GameOutcomeRepository $gameOutcomeRepository,
        private readonly UserRepository $userRepository,
        private readonly TransactionManager $transactionManager,
        private readonly RandomDiceNumberGenerator $rng
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     */
    public function handle(PlayDiceGameCommand $command): GameOutcome
    {
        $diceGame = $this->gameRepository->getDiceGameById(GameId::fromString($command->gameId));
        $user = $this->userRepository->getById(UserId::fromString($command->userId));

        $betAmount = BetAmount::fromInt($command->betAmount);

        $user->debit($betAmount);

        $playInput = new PlayDiceInput(
            userId: $user->getId(),
            betAmount: $betAmount,
            chosenNumber: DiceNumber::fromInt($command->chosenNumber),
            playDiceType: PlayDiceType::from($command->playDiceType)
        );

        $gameOutcome = $diceGame->playDice($playInput, $this->rng);

        $user->credit($gameOutcome->winAmount);

        return $this->transactionManager->transactional(function () use ($gameOutcome, $user) {
            $this->userRepository->save($user);
            $this->gameOutcomeRepository->save($gameOutcome);

            return $gameOutcome;
        });
    }
}
