<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
use App\Domain\Model\AccountRoles;
use App\Domain\Model\AccountStatus;
use App\Domain\Model\Common\DateTimeUtc;
use App\Domain\Model\Common\EmailAddress;
use App\Domain\Model\Common\HashedPassword;
use App\Domain\Model\LocaleCode;
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
            roles: AccountRoles::fromStrings(...$entity->roles),
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
