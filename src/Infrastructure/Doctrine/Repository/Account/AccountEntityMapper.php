<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\AccountRoleSet;
use App\Domain\Account\AccountStatus;
use App\Domain\Common\ValueObject\DateTimeUtc;
use App\Domain\Common\ValueObject\EmailAddress;
use App\Domain\Common\ValueObject\HashedPassword;
use App\Domain\Localization\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountEntityMapper
{
    public static function map(AccountEntity $entity): Account
    {
        return new Account(
            id: AccountIdentifier::fromString((string) $entity->id),
            createdAt: DateTimeUtc::fromInterface($entity->createdAt),
            email: EmailAddress::fromString($entity->email),
            password: HashedPassword::fromString($entity->password),
            locale: LocaleCode::fromString($entity->locale),
            roles: AccountRoleSet::fromStrings(...$entity->roles),
            status: AccountStatus::fromString($entity->status),
        );
    }

    /**
     * @return Account[]
     */
    public static function mapAll(AccountEntity ...$entities): array
    {
        return array_map(self::map(...), $entities);
    }
}
