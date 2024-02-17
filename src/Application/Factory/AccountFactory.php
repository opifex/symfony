<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use Symfony\Component\Uid\Uuid;

final class AccountFactory
{
    /**
     * @param string[] $roles
     */
    public static function createCustomAccount(string $email, string $password, string $locale, array $roles): Account
    {
        return new Account(Uuid::v7()->toRfc4122(), $email, $password, $locale, roles: $roles);
    }

    public static function createUserAccount(string $email, string $password, string $locale): Account
    {
        return new Account(Uuid::v7()->toRfc4122(), $email, $password, $locale, roles: [AccountRole::ROLE_USER]);
    }
}
