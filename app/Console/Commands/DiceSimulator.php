<?php

namespace App\Console\Commands;

use App\DTO\Api\Game\PlayGameDTO;
use App\Models\Game;
use App\Services\Game\GameResolver;
use App\Services\Game\PlayGameService;
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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gameId = $this->ask('Enter game ID');
        $game = Game::findOrFail($gameId);

        $betAmount = $this->ask('Enter bet amount');
        $diceAmount = $this->ask('Enter dice amount');
        $betType = $this->choice(
            'What is your bet type?',
            ['over', 'under'],
            'over'
        );

        $playGameService = app(PlayGameService::class);

        $gameResolver = app(GameResolver::class);

        $playGameDTO = new PlayGameDTO(
            amount: $betAmount,
            params: [
                'number' => $diceAmount,
                'bet_type' => $betType
            ]
        );

        $numberOfCycles = $this->ask('Enter number of simulation cycles');

        $totalBets = 0;
        $totalWins = 0;

        for ($i = 0; $i < $numberOfCycles; $i++) {
            $totalBets += $betAmount;

            $result = $playGameService->play($gameResolver, $game, $playGameDTO);
            $winAmount = $betAmount * $result['multiplier'];
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
