<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Exception\AuthorizationRequiredException;

interface AuthorizationTokenStorageInterface
{
    /**
     * @throws AuthorizationRequiredException
     */
    public function getUserIdentifier(): string;
}
