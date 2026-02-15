<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Enums\BetResult;
use Database\Factories\BetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property int $amount
 * @property array $bet_data
 * @property BetResult|null $result
 * @property int|null $payout
 */
class Bet extends Model
{
    /** @use HasFactory<BetFactory> */
    use HasFactory;

    protected $fillable = ['id', 'user_id', 'game_id', 'amount', 'bet_data', 'result', 'payout'];

    protected function casts(): array
    {
        return [
            'bet_data' => 'array',
            'result' => BetResult::class,
        ];
    }
}
