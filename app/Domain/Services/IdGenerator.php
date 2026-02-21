<?php

namespace App\Domain\Services;

interface IdGenerator
{
    public function generate(): string;
}
