<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface AuthenticationTokenInterface
{
    public function getSecret(): string;
}
