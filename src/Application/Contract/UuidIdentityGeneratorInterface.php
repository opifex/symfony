<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface UuidIdentityGeneratorInterface
{
    public function generate(): string;
}
