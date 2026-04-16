<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\AuthenticatedUser;

use Override;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TokenAuthenticatedUser implements UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private string $userIdentifier,
        private array $roles = [],
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
}
