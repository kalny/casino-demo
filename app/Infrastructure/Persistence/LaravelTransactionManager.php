<?php

namespace App\Infrastructure\Persistence;

use App\Application\Ports\TransactionManager;
use Illuminate\Support\Facades\DB;
use Throwable;

class LaravelTransactionManager implements TransactionManager
{
    /**
     * @throws Throwable
     */
    public function transactional(callable $operation): mixed
    {
        return DB::transaction($operation);
    }
}
