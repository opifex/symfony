<?php

declare(strict_types=1);

namespace App\Domain\Contract\Authorization;

use App\Domain\Exception\Authorization\AuthorizationRequiredException;
use App\Domain\Model\Role;

interface AuthorizationTokenManagerInterface
{
    /**
     * @throws AuthorizationRequiredException
     */
    public function getUserIdentifier(): string;

    /**
     * @throws AuthorizationRequiredException
     */
    public function checkUserPermission(Role $role, mixed $subject = null): void;
}
