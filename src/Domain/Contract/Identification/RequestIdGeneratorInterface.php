<?php

declare(strict_types=1);

namespace App\Domain\Contract\Identification;

interface RequestIdGeneratorInterface
{
    public function generate(): string;
}
