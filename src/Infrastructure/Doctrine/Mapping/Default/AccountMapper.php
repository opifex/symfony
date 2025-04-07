<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountMapper
{
    public static function mapOne(AccountEntity $account): Account
    {
        $accountStatus = AccountStatus::fromValue($account->status);

        return new Account(
            uuid: $account->uuid,
            email: $account->email,
            password: $account->password,
            locale: $account->locale,
            roles: $account->roles,
            status: $accountStatus,
            createdAt: $account->createdAt,
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
