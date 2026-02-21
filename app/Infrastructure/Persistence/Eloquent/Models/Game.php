<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\Game\GameType;
use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property GameType $type
 * @property array $config
 */
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'type', 'config'];

    protected function casts(): array
    {
        return [
            'type' => GameType::class,
            'config' => 'array',
        ];
    }
}
