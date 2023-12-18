<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\AccountInterface;
use DateTimeImmutable;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Account implements AccountInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private string $uuid,
        private string $email,
        private string $password = '',
        private string $locale = Locale::EN,
        private string $status = AccountStatus::CREATED,
        private array $roles = [AccountRole::ROLE_USER],
        private DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
    }

    #[Override]
    public function getUuid(): string
    {
        return $this->uuid;
    }

    #[Override]
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    #[Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(#[SensitiveParameter] string $password): self
    {
        $this->password = $password;

        return $this;
    }

    #[Override]
    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    #[Override]
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    #[Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    #[Override]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->getUuid();
    }

    #[Override]
    public function eraseCredentials(): void
    {
    }
}
