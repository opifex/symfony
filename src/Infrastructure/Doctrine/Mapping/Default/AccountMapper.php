<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountRoleCollection;
use App\Domain\Entity\AccountStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountMapper
{
    public static function mapOne(AccountEntity $account): Account
    {
        $accountStatus = AccountStatus::fromValue($account->status);
        $transformRoleClosure = static fn(string $role) => AccountRole::fromValue($role);
        $accountRoles = new AccountRoleCollection(...array_map($transformRoleClosure, $account->roles));

        return new Account(
            uuid: $account->uuid,
            email: $account->email,
            password: $account->password,
            locale: $account->locale,
            status: $accountStatus,
            roles: $accountRoles,
            createdAt: $account->createdAt,
        );
    }

    public static function mapMany(AccountEntity ...$account): AccountCollection
    {
        $transformAccountFunction = static fn(AccountEntity $account) => self::mapOne($account);

        return new AccountCollection(...array_map($transformAccountFunction, $account));
    }
}
