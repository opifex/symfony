<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

final readonly class JwtAccessToken
{
    /**
     * @param string[] $userRoles
     */
    public function __construct(
        public string $userIdentifier,
        public array $userRoles = [],
    ) {
    }
}
