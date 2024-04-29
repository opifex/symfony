<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $uuid,
        private readonly string $email,
        private readonly string $password,
        private readonly string $locale = 'en_US',
        private readonly string $status = AccountStatus::CREATED,
        private readonly array $roles = [AccountRole::ROLE_USER],
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    #[Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    #[Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->uuid;
    }

    #[Override]
    public function eraseCredentials(): void
    {
    }
}
