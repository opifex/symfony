<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Contract\AccountFactoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;

final class AccountFactory implements AccountFactoryInterface
{
    public function createCustomAccount(string $email, array $roles): Account
    {
        return new Account($email, $roles);
    }

    public function createUserAccount(string $email): Account
    {
        return $this->createCustomAccount($email, roles: [AccountRole::ROLE_USER]);
    }
}
