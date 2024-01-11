<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface AccessTokenInterface
{
    public function getSecret(): string;
}
