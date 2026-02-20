<?php

namespace App\Application\UseCase\PlayDiceGame;

use App\Application\Ports\TransactionManager;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\Dice\RandomDiceNumberGenerator;
use App\Domain\Games\Dice\ValueObjects\DiceNumber;
use App\Domain\Games\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Games\Dice\ValueObjects\PlayDiceType;
use App\Domain\Games\Repository\GameOutcomeRepository;
use App\Domain\Games\Repository\GameRepository;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\User\Repository\UserRepository;

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
        $diceGame = $this->gameRepository->getDiceGameById($command->gameId);
        $user = $this->userRepository->getById($command->userId);

        $betAmount = new BetAmount($command->betAmount);

        $user->debit($betAmount);

        $playInput = new PlayDiceInput(
            userId: $user->getId(),
            betAmount: $betAmount,
            chosenNumber: new DiceNumber($command->chosenNumber),
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
