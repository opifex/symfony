<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AuthorizationToken
{
    /**
     * @param string[] $userRoles
     */
    public function __construct(
        private readonly string $userIdentifier,
        private readonly array $userRoles = [],
    ) {
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return string[]
     */
    public function getUserRoles(): array
    {
        return $this->userRoles;
    }
}
