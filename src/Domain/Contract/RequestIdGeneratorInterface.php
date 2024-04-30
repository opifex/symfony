<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface RequestIdGeneratorInterface
{
    public function generate(): string;
}
