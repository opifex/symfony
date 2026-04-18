<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Exception\AuthorizationRequiredException;
use DateTimeImmutable;

interface AuthorizationTokenStorageInterface
{
    /**
     * @throws AuthorizationRequiredException
     */
    public function getTokenIdentifier(): string;

    /**
     * @throws AuthorizationRequiredException
     */
    public function getTokenExpiresAt(): DateTimeImmutable;

    /**
     * @throws AuthorizationRequiredException
     */
    public function getUserIdentifier(): string;
}
