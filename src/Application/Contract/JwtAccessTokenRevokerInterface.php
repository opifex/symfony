<?php

declare(strict_types=1);

namespace App\Application\Contract;

use DateTimeImmutable;

interface JwtAccessTokenRevokerInterface
{
    public function revoke(string $tokenIdentifier, DateTimeImmutable $expiresAt): void;

    public function isRevoked(string $tokenIdentifier): bool;
}
