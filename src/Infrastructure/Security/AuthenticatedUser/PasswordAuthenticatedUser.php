<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\AuthenticatedUser;

use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class PasswordAuthenticatedUser implements PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private string $userIdentifier,
        private string $password,
        private array $roles = [],
        private bool $enabled = true,
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

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
