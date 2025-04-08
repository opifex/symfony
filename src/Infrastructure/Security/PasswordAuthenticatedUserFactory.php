<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class PasswordAuthenticatedUserFactory
{
    public static function createFromAccount(Account $account): PasswordAuthenticatedUser
    {
        return new PasswordAuthenticatedUser(
            identifier: $account->getUuid(),
            password: $account->getPassword(),
            roles: $account->getRoles(),
            enabled: $account->getStatus() === AccountStatus::ACTIVATED,
        );
    }
}
