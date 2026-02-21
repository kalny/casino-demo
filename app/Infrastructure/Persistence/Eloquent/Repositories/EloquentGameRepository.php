<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Common\Exceptions\InvalidArgumentException;
use App\Domain\Game\Exceptions\InvalidGameConfigException;
use App\Domain\Game\GameType;
use App\Domain\Game\Dice\DiceGame;
use App\Domain\Game\GameId;
use App\Domain\Game\Repository\GameRepository;
use App\Domain\Game\Slot\SlotGame;
use App\Domain\Game\Slot\ValueObjects\GridInt;
use App\Domain\Game\Slot\ValueObjects\Paylines;
use App\Domain\Game\Slot\ValueObjects\ReelStrip;
use App\Domain\Game\Slot\ValueObjects\SymbolsCollection;
use App\Domain\Common\ValueObjects\BetMultiplier;
use App\Infrastructure\Persistence\Eloquent\Models\Game as GameEloquentModel;

class EloquentGameRepository implements GameRepository
{
    public function getTypeById(GameId $id): GameType
    {
        $gameEloquentModel = GameEloquentModel::query()
            ->findOrFail($id->getValue());

        return $gameEloquentModel->type;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidGameConfigException
     */
    public function getDiceGameById(GameId $id): DiceGame
    {
        $gameEloquentModel = GameEloquentModel::query()
            ->where('id', $id->getValue())
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
    public function getSlotGameById(GameId $id): SlotGame
    {
        $gameEloquentModel = GameEloquentModel::query()
            ->where('id', $id->getValue())
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
