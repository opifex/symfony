<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Account\Account;
use App\Domain\Entity\Account\AccountRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountFactory
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @param string[] $roles
     */
    public function createCustomAccount(string $email, string $password, array $roles): Account
    {
        $account = new Account($email, $roles);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $password));

        return $account;
    }

    public function createUserAccount(string $email, string $password, string $locale): Account
    {
        $account = $this->createCustomAccount($email, $password, roles: [AccountRole::ROLE_USER]);
        $account->setLocale($locale);

        return $account;
    }
}
