<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapping\Default;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use Countable;
use IteratorAggregate;
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
            status: $account->getStatus(),
        );
    }

    public static function mapOne(AccountEntity $account): Account
    {
        return new Account(
            uuid: $account->uuid,
            email: $account->email,
            password: $account->password,
            locale: $account->locale,
            status: $account->status,
            roles: $account->roles,
            createdAt: $account->createdAt,
        );
    }

    /**
     * @param Countable&IteratorAggregate<int, AccountEntity> $accounts
     */
    public static function mapMany(Countable&IteratorAggregate $accounts): AccountCollection
    {
        return new AccountCollection(
            accounts: array_map(
                callback: static fn(AccountEntity $account) => self::mapOne($account),
                array: iterator_to_array($accounts),
            ),
            count: $accounts->count(),
        );
    }
}
