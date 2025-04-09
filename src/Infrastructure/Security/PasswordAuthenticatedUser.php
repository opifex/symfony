<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Exclude]
final class PasswordAuthenticatedUser implements PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $userIdentifier,
        private readonly string $password,
        private readonly array $roles = [],
        private readonly bool $enabled = true,
    ) {
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->userIdentifier;
    }

    #[Override]
    public function getPassword(): string
    {
        return $this->password;
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

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
