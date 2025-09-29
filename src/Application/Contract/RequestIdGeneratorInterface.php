<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface RequestIdGeneratorInterface
{
    public function generate(): string;
}
