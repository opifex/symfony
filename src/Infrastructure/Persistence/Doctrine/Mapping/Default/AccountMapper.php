<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Mapping\Default;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountRoleCollection;
use App\Domain\Entity\AccountStatus;
use DateTimeImmutable;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;

#[Exclude]
final class AccountMapper
{
    public static function mapEntity(string $email, #[SensitiveParameter] string $password): AccountEntity
    {
        return new AccountEntity(
            uuid: Uuid::v7()->toRfc4122(),
            createdAt: new DateTimeImmutable(),
            email: $email,
            password: $password,
            locale: 'en_US',
            roles: [],
            status: AccountStatus::Created->value,
        );
    }

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
