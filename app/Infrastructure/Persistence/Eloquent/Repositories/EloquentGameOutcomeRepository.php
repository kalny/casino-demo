<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Games\Common\GameOutcome;
use App\Domain\Games\Repository\GameOutcomeRepository;
use App\Infrastructure\Persistence\Eloquent\Models\Bet as BetEloquentModel;
use ReflectionClass;

class EloquentGameOutcomeRepository implements GameOutcomeRepository
{
    public function save(GameOutcome $gameOutcome): void
    {
        $reflection = new ReflectionClass($gameOutcome);

        $betData = $this->getPrivateProperty($reflection, $gameOutcome, 'gameSpecificOutcome');

        BetEloquentModel::create([
            'user_id' => $this->getPrivateProperty($reflection, $gameOutcome, 'userId')->getValue(),
            'game_id' => $this->getPrivateProperty($reflection, $gameOutcome, 'gameId')->getValue(),
            'amount' => $this->getPrivateProperty($reflection, $gameOutcome, 'betAmount')->getValue(),
            'bet_data' => $betData->jsonSerialize(),
            'result' => $this->getPrivateProperty($reflection, $gameOutcome, 'outcomeStatus')->value,
            'payout' => $this->getPrivateProperty($reflection, $gameOutcome, 'winAmount')->getValue(),
        ]);
    }

    private function getPrivateProperty(ReflectionClass $ref, object $object, string $propertyName): mixed
    {
        $property = $ref->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
