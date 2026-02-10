<?php

namespace App\Console\Commands;

use App\DTO\Api\Game\PlayGameDTO;
use App\Models\Game;
use App\Services\Game\GameResolver;
use App\Services\Game\PlayGameService;
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

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $gameId = $this->ask('Enter game ID');
        $game = Game::findOrFail($gameId);

        $betAmount = $this->ask('Enter bet amount');

        $playGameService = app(PlayGameService::class);

        $gameResolver = app(GameResolver::class);

        $playGameDTO = new PlayGameDTO(
            amount: $betAmount
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
