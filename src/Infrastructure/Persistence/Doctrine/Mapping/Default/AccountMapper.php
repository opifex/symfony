<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapping\Default;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountMapper
{
    public static function mapEntity(Account $account): AccountEntity
    {
        return new AccountEntity(
            uuid: $account->getUuid(),
            createdAt: $account->getCreatedAt(),
            email: $account->getEmail(),
            password: $account->getPassword(),
            locale: $account->getLocale(),
            roles: $account->getRoles(),
            status: $account->getStatus()->value,
        );
    }

    public static function mapOne(AccountEntity $account): Account
    {
        return new Account(
            uuid: $account->uuid,
            email: $account->email,
            password: $account->password,
            locale: $account->locale,
            status: AccountStatus::fromValue($account->status),
            roles: $account->roles,
            createdAt: $account->createdAt,
        );
    }

    public static function mapMany(AccountEntity ...$account): AccountCollection
    {
        $callback = static fn(AccountEntity $account) => self::mapOne($account);

        return new AccountCollection(...array_map($callback, $account));
    }
}
