<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\ActivatedUserInterface;
use DateTimeImmutable;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Exclude]
class Account implements ActivatedUserInterface, PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $uuid,
        private readonly string $email,
        private readonly string $password,
        private readonly string $locale,
        private readonly string $status,
        private readonly array $roles,
        private readonly DateTimeImmutable $createdAt,
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
    public function isActivated(): bool
    {
        return $this->status === AccountStatus::ACTIVATED;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    #[Override]
    public function eraseCredentials(): void
    {
        // Nothing to do
    }
}
