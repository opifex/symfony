<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Contract\AccountFactoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountFactory implements AccountFactoryInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function createCustomAccount(string $email, string $password, array $roles): Account
    {
        $account = new Account($email, $roles);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $password));

        return $account;
    }

    public function createUserAccount(string $email, string $password): Account
    {
        return $this->createCustomAccount($email, $password, roles: [AccountRole::ROLE_USER]);
    }
}
