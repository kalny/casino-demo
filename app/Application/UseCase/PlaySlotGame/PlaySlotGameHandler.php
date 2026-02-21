<?php

namespace App\Application\UseCase\PlaySlotGame;

use App\Application\Ports\TransactionManager;
use App\Domain\Common\Exceptions\InsufficientFundsException;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameOutcome;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameOutcomeRepository;
use App\Domain\Game\Repository\GameRepository;
use App\Domain\Game\Slot\RandomGridGenerator;
use App\Domain\Game\Slot\ValueObjects\PlaySlotInput;
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
        $slotGame = $this->gameRepository->getSlotGameById(GameId::fromString($command->gameId));
        $user = $this->userRepository->getById(UserId::fromString($command->userId));

        $betAmount = BetAmount::fromInt($command->betAmount);

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
