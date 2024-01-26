<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface AuthorizationTokenInterface
{
    public function getSecret(): string;
}
