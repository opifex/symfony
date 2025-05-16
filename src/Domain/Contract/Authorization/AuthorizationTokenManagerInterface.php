<?php

declare(strict_types=1);

namespace App\Domain\Contract\Authorization;

use App\Domain\Exception\Authorization\AuthorizationRequiredException;

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
