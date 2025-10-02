<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Exception\AuthorizationForbiddenException;
use App\Application\Exception\AuthorizationRequiredException;
use App\Domain\Security\Role;

interface AuthorizationTokenManagerInterface
{
    public function getUserIdentifier(): ?string;

    /**
     * @throws AuthorizationForbiddenException
     * @throws AuthorizationRequiredException
     */
    public function checkUserPermission(Role $role, mixed $subject = null): void;
}
