<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class AccountUserFactory
{
    public static function createFromAccount(Account $account): AccountUser
    {
        return new AccountUser(
            identifier: $account->getUuid(),
            password: $account->getPassword(),
            roles: $account->getRoles(),
            activated: $account->getStatus() === AccountStatus::ACTIVATED,
        );
    }
}
