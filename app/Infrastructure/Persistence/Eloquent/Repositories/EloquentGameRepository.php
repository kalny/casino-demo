<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Exceptions\InvalidArgumentException;
use App\Domain\Exceptions\InvalidGameConfigException;
use App\Domain\Games\Common\GameType;
use App\Domain\Games\Dice\DiceGame;
use App\Domain\Games\GameId;
use App\Domain\Games\Repository\GameRepository;
use App\Domain\Games\Slot\SlotGame;
use App\Domain\Games\Slot\ValueObjects\GridInt;
use App\Domain\Games\Slot\ValueObjects\Paylines;
use App\Domain\Games\Slot\ValueObjects\ReelStrip;
use App\Domain\Games\Slot\ValueObjects\SymbolsCollection;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Infrastructure\Persistence\Eloquent\Models\Game as GameEloquentModel;

class EloquentGameRepository implements GameRepository
{
    public function getTypeById(int $id): GameType
    {
        $gameEloquentModel = GameEloquentModel::query()
            ->findOrFail($id);

        return $gameEloquentModel->type;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidGameConfigException
     */
    public function getDiceGameById(int $id): DiceGame
    {
        $gameEloquentModel = GameEloquentModel::query()
            ->where('id', $id)
            ->where('type', GameType::Dice->value)
            ->firstOrFail();

        if (!isset($gameEloquentModel->config['multiplier'])) {
            throw new InvalidGameConfigException('Invalid game config. Multiplier missed');
        }

        return new DiceGame(
            gameId: new GameId($gameEloquentModel->id),
            name: $gameEloquentModel->name,
            multiplier: new BetMultiplier($gameEloquentModel->config['multiplier'])
        );
    }

    /**
     * @throws InvalidGameConfigException
     * @throws InvalidArgumentException
     */
    public function getSlotGameById(int $id): SlotGame
    {
        $gameEloquentModel = GameEloquentModel::query()
            ->where('id', $id)
            ->where('type', GameType::Slot->value)
            ->firstOrFail();

        if (!isset($gameEloquentModel->config['reels_number'])) {
            throw new InvalidGameConfigException('Invalid game config. reels_number missed');
        }

        if (!isset($gameEloquentModel->config['symbols_number'])) {
            throw new InvalidGameConfigException('Invalid game config. symbols_number missed');
        }

        if (!isset($gameEloquentModel->config['reel_strip'])) {
            throw new InvalidGameConfigException('Invalid game config. reel_strip missed');
        }

        if (!isset($gameEloquentModel->config['symbols'])) {
            throw new InvalidGameConfigException('Invalid game config. symbols missed');
        }

        if (!isset($gameEloquentModel->config['paylines'])) {
            throw new InvalidGameConfigException('Invalid game config. paylines missed');
        }

        return new SlotGame(
            gameId: new GameId($gameEloquentModel->id),
            name: $gameEloquentModel->name,
            reelsNumber: new GridInt($gameEloquentModel->config['reels_number']),
            symbolsNumber: new GridInt($gameEloquentModel->config['symbols_number']),
            reelStrip: new ReelStrip(
                $gameEloquentModel->config['reel_strip'],
                new SymbolsCollection($gameEloquentModel->config['symbols'])
            ),
            paylines: new Paylines($gameEloquentModel->config['paylines'])
        );
    }
}
