<?php

namespace App\Console\Commands;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameRepository;
use App\Domain\Game\Slot\RandomGridGenerator;
use App\Domain\Game\Slot\ValueObjects\PlaySlotInput;
use App\Application\Services\IdGenerator;
use App\Domain\User\UserId;
use App\Infrastructure\Services\PHPSeededRandomNumberGenerator;
use Illuminate\Console\Command;

class SlotSimulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slot:simulate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate Slot RTP';

    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly IdGenerator $idGenerator,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        $gameId = $this->ask('Enter Slot Game ID');
        $slotGame = $this->gameRepository->getSlotGameById(GameId::fromString($gameId));

        $betAmount = $this->ask('Enter bet amount');

        $playInput = new PlaySlotInput(
            userId: UserId::fromString($this->idGenerator->generate()), // fake
            betAmount: new BetAmount($betAmount)
        );

        $numberOfCycles = $this->ask('Enter number of simulation cycles');

        $seed = $this->ask('Enter seed');

        $rgg = new RandomGridGenerator(
            new PHPSeededRandomNumberGenerator($seed)
        );

        $totalBets = 0;
        $totalWins = 0;

        for ($i = 0; $i < $numberOfCycles; $i++) {
            $totalBets += $betAmount;

            $gameOutcome = $slotGame->playSlot($playInput, $rgg);

            $winAmount = $gameOutcome->winAmount->getValue();

            if ($winAmount > 0) {
                $totalWins += $winAmount;
            }
        }

        $this->info('Done!');
        $this->info('Total bets: ' . $totalBets);
        $this->info('Total wins: ' . $totalWins);

        $rtp = $totalWins / $totalBets;

        $this->info('RTP: ' . $rtp);
    }
}
