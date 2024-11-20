<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Account
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $uuid,
        private readonly string $email,
        private readonly string $password,
        private readonly string $locale,
        private readonly AccountStatus $status,
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getStatus(): AccountStatus
    {
        return $this->status;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isActivated(): bool
    {
        return $this->status === AccountStatus::Activated;
    }
}
