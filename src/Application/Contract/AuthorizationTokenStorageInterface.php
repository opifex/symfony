<?php

declare(strict_types=1);

namespace App\Application\Contract;

use DateTimeImmutable;

interface AuthorizationTokenStorageInterface
{
    public function getTokenIdentifier(): string;

    public function getTokenExpiresAt(): DateTimeImmutable;

    public function getUserIdentifier(): string;
}
