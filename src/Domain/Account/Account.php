<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Domain\Foundation\ValueObject\DateTimeUtc;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use NoDiscard;

final class Account
{
    public function __construct(
        public readonly AccountIdentifier $id,
        public readonly DateTimeUtc $createdAt,
        public readonly EmailAddress $email,
        public readonly PasswordHash $password,
        public readonly LocaleCode $locale,
        public readonly AccountRoleSet $roles,
        public readonly AccountStatus $status,
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
