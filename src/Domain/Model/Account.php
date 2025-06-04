<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Common\DateTimeUtc;
use App\Domain\Model\Common\EmailAddress;
use App\Domain\Model\Common\HashedPassword;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Account
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private AccountIdentifier $id,
        private DateTimeUtc $createdAt,
        private EmailAddress $email,
        private HashedPassword $password,
        private LocaleCode $locale,
        private array $roles,
        private string $status,
    ) {
    }

    public static function create(string $email, string $hashedPassword, string $locale): self
    {
        return new self(
            id: AccountIdentifier::generate(),
            createdAt: DateTimeUtc::now(),
            email: EmailAddress::fromString($email),
            password: HashedPassword::fromString($hashedPassword),
            locale: LocaleCode::fromString($locale),
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
    }

    public function getId(): AccountIdentifier
    {
        return $this->id;
    }

    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }

    public function getLocale(): LocaleCode
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

    public function getCreatedAt(): DateTimeUtc
    {
        return $this->createdAt;
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::ACTIVATED;
    }

    public function changeEmail(EmailAddress $email): void
    {
        $this->email = $email;
    }

    public function changePassword(HashedPassword $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function switchLocale(LocaleCode $locale): void
    {
        $this->locale = $locale;
    }

    public function updateStatus(string $status): void
    {
        $this->status = $status;
    }
}
