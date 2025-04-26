<?php

declare(strict_types=1);

namespace App\Domain\Model;

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
        private readonly DateTimeImmutable $createdAt,
        private readonly string $email,
        private readonly string $password,
        private readonly string $locale,
        private readonly array $roles,
        private readonly string $status,
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

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::ACTIVATED;
    }
}
