<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use App\Domain\Model\Account;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountEntityMapper
{
    public static function map(AccountEntity $entity): Account
    {
        return new Account(
            uuid: $entity->uuid ?? throw new LogicException(message: 'UUID is required.'),
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
