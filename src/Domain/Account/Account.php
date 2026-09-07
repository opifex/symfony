<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Domain\Account\Exception\AccountInvalidActionException;
use App\Domain\Foundation\DomainEventsTrait;
use App\Domain\Foundation\ImmutableCloneTrait;
use App\Domain\Foundation\ValueObject\DateTimeUtc;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use NoDiscard;

final readonly class Account
{
    use DomainEventsTrait;
    use ImmutableCloneTrait;

    /**
     * @param object[] $events
     */
    public function __construct(
        public AccountIdentifier $id,
        public EmailAddress $email,
        public PasswordHash $password,
        public LocaleCode $locale,
        public AccountRoleSet $roles,
        public AccountStatus $status,
        public DateTimeUtc $createdAt,
        public ?DateTimeUtc $updatedAt = null,
        public ?DateTimeUtc $deletedAt = null,
        public int $version = 1,
        private array $events = [],
    ) {
    }

    public static function create(
        AccountIdentifier $id,
        EmailAddress $email,
        PasswordHash $password,
        LocaleCode $locale,
    ): self {
        return new self(
            id: $id,
            email: $email,
            password: $password,
            locale: $locale,
            roles: AccountRoleSet::fromRoles(roles: AccountRole::User),
            status: AccountStatus::Created,
            createdAt: DateTimeUtc::now(),
        );
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Activated;
    }

    #[NoDiscard]
    public function withEmail(EmailAddress $email): self
    {
        return $this->withFields(['email' => $email]);
    }

    #[NoDiscard]
    public function withPassword(PasswordHash $hashedPassword): self
    {
        return $this->withFields(['password' => $hashedPassword]);
    }

    #[NoDiscard]
    public function withLocale(LocaleCode $locale): self
    {
        return $this->withFields(['locale' => $locale]);
    }

    #[NoDiscard]
    public function register(): self
    {
        if ($this->status !== AccountStatus::Created) {
            throw AccountInvalidActionException::create();
        }

        $account = $this->withFields(['status' => AccountStatus::Registered]);

        return $account->withEvents(AccountRegisteredEvent::create($account));
    }

    #[NoDiscard]
    public function activate(): self
    {
        if ($this->status !== AccountStatus::Registered) {
            throw AccountInvalidActionException::create();
        }

        return $this->withFields(['status' => AccountStatus::Activated]);
    }

    #[NoDiscard]
    public function block(): self
    {
        if ($this->status !== AccountStatus::Activated) {
            throw AccountInvalidActionException::create();
        }

        return $this->withFields(['status' => AccountStatus::Blocked]);
    }

    #[NoDiscard]
    public function unblock(): self
    {
        if ($this->status !== AccountStatus::Blocked) {
            throw AccountInvalidActionException::create();
        }

        return $this->withFields(['status' => AccountStatus::Activated]);
    }

    #[NoDiscard]
    public function delete(): self
    {
        if ($this->deletedAt !== null) {
            throw AccountInvalidActionException::create();
        }

        return $this->withFields(['deletedAt' => DateTimeUtc::now()]);
    }
}
