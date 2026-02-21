<?php

namespace App\Console\Commands;

use App\Domain\Common\ValueObjects\BetAmount;
use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Dice\RandomDiceNumberGenerator;
use App\Domain\Game\Dice\ValueObjects\DiceNumber;
use App\Domain\Game\Dice\ValueObjects\PlayDiceInput;
use App\Domain\Game\Dice\ValueObjects\PlayDiceType;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameRepository;
use App\Application\Services\IdGenerator;
use App\Domain\User\UserId;
use App\Infrastructure\Services\PHPSeededRandomNumberGenerator;
use Illuminate\Console\Command;

class DiceSimulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dice:simulate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate Dice RTP';

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
        $gameId = $this->ask('Enter Dice Game ID');
        $diceGame = $this->gameRepository->getDiceGameById(GameId::fromString($gameId));

        $betAmount = $this->ask('Enter bet amount');
        $diceNumber = $this->ask('Enter dice number');
        $playDiceType = $this->choice(
            'What is your bet type (over, under)?',
            ['over', 'under'],
            'over'
        );

        $playInput = new PlayDiceInput(
            userId: UserId::fromString($this->idGenerator->generate()), // fake
            betAmount: new BetAmount($betAmount),
            chosenNumber: DiceNumber::fromInt($diceNumber),
            playDiceType: PlayDiceType::from($playDiceType)
        );

        $numberOfCycles = $this->ask('Enter number of simulation cycles');

        $seed = $this->ask('Enter seed');

        $rng = new RandomDiceNumberGenerator(
            new PHPSeededRandomNumberGenerator($seed)
        );

        $totalBets = 0;
        $totalWins = 0;

        for ($i = 0; $i < $numberOfCycles; $i++) {
            $totalBets += $betAmount;

            $gameOutcome = $diceGame->playDice($playInput, $rng);

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
