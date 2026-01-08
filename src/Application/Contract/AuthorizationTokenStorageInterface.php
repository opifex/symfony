<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface AuthorizationTokenStorageInterface
{
    public function getUserIdentifier(): ?string;
}
