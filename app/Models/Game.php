<?php

namespace App\Models;

use App\Enums\GameType;
use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property GameType $type
 * @property array $config
 */
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    protected $fillable = ['name', 'type', 'config'];

    protected function casts(): array
    {
        return [
            'type' => GameType::class,
            'config' => 'array',
        ];
    }
}
