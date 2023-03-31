<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Account\Account;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountFactory
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @param string[] $roles
     */
    public function create(string $email, string $password, array $roles): Account
    {
        $account = new Account($email, $roles);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $password));

        return $account;
    }
}
