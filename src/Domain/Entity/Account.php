<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Account
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $email,
        private readonly string $password,
        private readonly string $locale,
        private readonly AccountStatus $status = AccountStatus::Created,
        private readonly AccountRoleCollection $roles = new AccountRoleCollection(),
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

    public function getRoles(): AccountRoleCollection
    {
        return $this->roles;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
