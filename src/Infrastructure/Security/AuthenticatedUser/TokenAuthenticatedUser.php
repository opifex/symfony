<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\AuthenticatedUser;

use Override;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenAuthenticatedUser implements UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $userIdentifier,
        private readonly array $roles = [],
    ) {
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->userIdentifier;
    }

    #[Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    #[Override]
    public function eraseCredentials(): void
    {
        // Nothing to do
    }
}
