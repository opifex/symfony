<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
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
