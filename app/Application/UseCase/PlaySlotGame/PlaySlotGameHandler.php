<?php

namespace App\Application\UseCase\PlaySlotGame;

use App\Application\Ports\TransactionManager;
use App\Domain\Exceptions\InsufficientFundsException;
use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\GameId;
use App\Domain\Games\Repository\GameOutcomeRepository;
use App\Domain\Games\Repository\GameRepository;
use App\Domain\Games\Slot\RandomGridGenerator;
use App\Domain\Games\Slot\ValueObjects\PlaySlotInput;
use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UserId;

class PlaySlotGameHandler
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly GameOutcomeRepository $gameOutcomeRepository,
        private readonly UserRepository $userRepository,
        private readonly TransactionManager $transactionManager,
        private readonly RandomGridGenerator $rgg
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws InsufficientFundsException
     */
    public function handle(PlaySlotGameCommand $command): GameOutcome
    {
        $slotGame = $this->gameRepository->getSlotGameById(new GameId($command->gameId));
        $user = $this->userRepository->getById(new UserId($command->userId));

        $betAmount = new BetAmount($command->betAmount);

        $user->debit($betAmount);

        $playInput = new PlaySlotInput(
            userId: $user->getId(),
            betAmount: $betAmount
        );

        $gameOutcome = $slotGame->playSlot($playInput, $this->rgg);

        $user->credit($gameOutcome->winAmount);

        return $this->transactionManager->transactional(function () use ($gameOutcome, $user) {
            $this->userRepository->save($user);
            $this->gameOutcomeRepository->save($gameOutcome);

            return $gameOutcome;
        });
    }
}
