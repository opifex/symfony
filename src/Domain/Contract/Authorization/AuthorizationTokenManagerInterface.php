<?php

declare(strict_types=1);

namespace App\Domain\Contract\Authorization;

use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Exception\Authorization\AuthorizationRequiredException;
use App\Domain\Model\Role;

interface AuthorizationTokenManagerInterface
{
    public function getUserIdentifier(): ?string;

    /**
     * @throws AuthorizationForbiddenException
     * @throws AuthorizationRequiredException
     */
    public function checkUserPermission(Role $role, mixed $subject = null): void;
}
