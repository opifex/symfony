<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\AuthorizationRequiredException;

interface AuthorizationTokenManagerInterface
{
    /**
     * @throws AuthorizationRequiredException
     */
    public function getUserIdentifier(): string;

    /**
     * @throws AuthorizationRequiredException
     */
    public function checkPermission(string $access, mixed $subject = null): bool;
}
