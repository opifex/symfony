<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Account;
use Symfony\Component\Uid\Uuid;

final class AccountFactory
{
    /**
     * @param string[] $roles
     */
    public static function createCustomAccount(string $email, string $locale = 'en', array $roles = []): Account
    {
        return new Account(Uuid::v7()->toRfc4122(), $email, locale: $locale, roles: $roles);
    }

    public static function createUserAccount(string $email, string $locale = 'en'): Account
    {
        return new Account(Uuid::v7()->toRfc4122(), $email, locale: $locale);
    }
}
