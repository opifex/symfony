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
    public static function createCustomAccount(string $email, array $roles): Account
    {
        return new Account(uuid: Uuid::v7()->toRfc4122(), email: $email, roles: $roles);
    }

    public static function createUserAccount(string $email): Account
    {
        return self::createCustomAccount($email, roles: [AccountRole::ROLE_USER]);
    }
}
