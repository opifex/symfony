<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Domain\Account\Exception\AccountInvalidActionException;
use App\Domain\Foundation\ValueObject\DateTimeUtc;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use NoDiscard;

final readonly class Account
{
    public function __construct(
        public AccountIdentifier $id,
        public EmailAddress $email,
        public PasswordHash $password,
        public LocaleCode $locale,
        public AccountRoleSet $roles,
        public AccountStatus $status,
        public DateTimeUtc $createdAt,
        public DateTimeUtc $updatedAt,
    ) {
    }

    public static function create(AccountIdentifier $id, EmailAddress $email, PasswordHash $password): self
    {
        return new self(
            id: $id,
            email: $email,
            password: $password,
            locale: LocaleCode::EnUs,
            roles: AccountRoleSet::fromStrings(AccountRole::User->toString()),
            status: AccountStatus::Created,
            createdAt: DateTimeUtc::now(),
            updatedAt: DateTimeUtc::now(),
        );
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Activated;
    }

    #[NoDiscard]
    public function withEmail(EmailAddress $email): self
    {
        return clone($this, [
            'email' => $email,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }

    #[NoDiscard]
    public function withPassword(PasswordHash $hashedPassword): self
    {
        return clone($this, [
            'password' => $hashedPassword,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }

    #[NoDiscard]
    public function withLocale(LocaleCode $locale): self
    {
        return clone($this, [
            'locale' => $locale,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }

    #[NoDiscard]
    public function register(): self
    {
        if ($this->status !== AccountStatus::Created) {
            throw AccountInvalidActionException::create();
        }

        return clone($this, [
            'status' => AccountStatus::Registered,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }

    #[NoDiscard]
    public function activate(): self
    {
        if ($this->status !== AccountStatus::Registered) {
            throw AccountInvalidActionException::create();
        }

        return clone($this, [
            'status' => AccountStatus::Activated,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }

    #[NoDiscard]
    public function block(): self
    {
        if ($this->status !== AccountStatus::Activated) {
            throw AccountInvalidActionException::create();
        }

        return clone($this, [
            'status' => AccountStatus::Blocked,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }

    #[NoDiscard]
    public function unblock(): self
    {
        if ($this->status !== AccountStatus::Blocked) {
            throw AccountInvalidActionException::create();
        }

        return clone($this, [
            'status' => AccountStatus::Activated,
            'updatedAt' => DateTimeUtc::now(),
        ]);
    }
}
