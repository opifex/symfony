<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Model\Account;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountEntityMapper
{
    public static function mapOne(AccountEntity $account): Account
    {
        return new Account(
            uuid: $account->uuid,
            createdAt: $account->createdAt,
            email: $account->email,
            password: $account->password,
            locale: $account->locale,
            roles: $account->roles,
            status: $account->status,
        );
    }

    /**
     * @return Account[]
     */
    public static function mapMany(AccountEntity ...$account): array
    {
        return array_map(static fn(AccountEntity $account): Account => self::mapOne($account), $account);
    }
}
