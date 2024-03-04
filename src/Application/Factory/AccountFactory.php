<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use Symfony\Component\Uid\Uuid;

final class AccountFactory
{
    /**
     * @param string[] $accessRoles
     */
    public static function createCustomAccount(
        string $emailAddress,
        string $hashedPassword,
        string $defaultLocale,
        array $accessRoles,
    ): Account {
        return new Account(
            uuid: Uuid::v7()->toRfc4122(),
            email: $emailAddress,
            password: $hashedPassword,
            locale: $defaultLocale,
            roles: $accessRoles,
        );
    }

    public static function createUserAccount(
        string $emailAddress,
        string $hashedPassword,
        string $defaultLocale,
    ): Account {
        return new Account(
            uuid: Uuid::v7()->toRfc4122(),
            email: $emailAddress,
            password: $hashedPassword,
            locale: $defaultLocale,
            roles: [AccountRole::ROLE_USER],
        );
    }
}
