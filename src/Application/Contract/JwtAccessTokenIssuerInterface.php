<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface JwtAccessTokenIssuerInterface
{
    /**
     * @param string[] $userRoles
     */
    public function issue(string $userIdentifier, array $userRoles = []): string;

    public function lifetime(): int;
}
