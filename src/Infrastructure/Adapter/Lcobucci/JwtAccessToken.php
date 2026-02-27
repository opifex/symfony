<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

final class JwtAccessToken
{
    /**
     * @param string[] $userRoles
     */
    public function __construct(
        public readonly string $userIdentifier,
        public readonly array $userRoles = [],
    ) {
    }
}
