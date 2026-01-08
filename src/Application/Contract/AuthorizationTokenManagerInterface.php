<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface AuthorizationTokenManagerInterface
{
    public function getUserIdentifier(): ?string;
}
