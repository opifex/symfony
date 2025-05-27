<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountEntityMapper
{
    public static function map(AccountEntity $entity): Account
    {
        return new Account(
            id: new AccountIdentifier((string) $entity->id),
            createdAt: $entity->createdAt,
            email: $entity->email,
            password: $entity->password,
            locale: $entity->locale,
            roles: $entity->roles,
            status: $entity->status,
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
