<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

class AuthorizationToken
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
