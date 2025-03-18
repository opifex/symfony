<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Exclude]
final class AccountUser implements PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $identifier,
        private readonly string $password,
        private readonly array $roles,
        private readonly bool $activated,
    ) {
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->identifier;
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

    public function isActivated(): bool
    {
        return $this->activated;
    }
}
