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
        private AccountIdentifier $id,
        private DateTimeImmutable $createdAt,
        private string $email,
        private string $password,
        private string $locale,
        private array $roles,
        private string $status,
    ) {
    }

    public static function create(string $email, string $hashedPassword, string $locale): self
    {
        return new self(
            id: AccountIdentifier::generate(),
            createdAt: new DateTimeImmutable(),
            email: $email,
            password: $hashedPassword,
            locale: $locale,
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
    }

    public function getId(): AccountIdentifier
    {
        return $this->id;
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

    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function switchLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function updateStatus(string $status): void
    {
        $this->status = $status;
    }
}
