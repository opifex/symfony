<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface AuthenticationRateLimiterInterface
{
    public function isAccepted(string $key): bool;
}
