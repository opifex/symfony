<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Domain\Foundation\ValueObject\DateTimeUtc;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use NoDiscard;

class Account
{
    public function __construct(
        private readonly AccountIdentifier $id,
        private readonly DateTimeUtc $createdAt,
        private readonly EmailAddress $email,
        private readonly PasswordHash $password,
        private readonly LocaleCode $locale,
        private readonly AccountRoleSet $roles,
        private readonly AccountStatus $status,
    ) {
    }

    public static function create(AccountIdentifier $id, EmailAddress $email, PasswordHash $password): self
    {
        return new self(
            id: $id,
            createdAt: DateTimeUtc::now(),
            email: $email,
            password: $password,
            locale: LocaleCode::EnUs,
            roles: AccountRoleSet::fromStrings(AccountRole::User->toString()),
            status: AccountStatus::Created,
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

    public function getPassword(): PasswordHash
    {
        return $this->password;
    }

    public function getLocale(): LocaleCode
    {
        return $this->locale;
    }

    public function getRoles(): AccountRoleSet
    {
        return $this->roles;
    }

    public function getStatus(): AccountStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeUtc
    {
        return $this->createdAt;
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Activated;
    }

    #[NoDiscard]
    public function withEmail(EmailAddress $email): self
    {
        return clone($this, ['email' => $email]);
    }

    #[NoDiscard]
    public function withPassword(PasswordHash $hashedPassword): self
    {
        return clone($this, ['password' => $hashedPassword]);
    }

    #[NoDiscard]
    public function withLocale(LocaleCode $locale): self
    {
        return clone($this, ['locale' => $locale]);
    }

    #[NoDiscard]
    public function withStatus(AccountStatus $status): self
    {
        return clone($this, ['status' => $status]);
    }
}
