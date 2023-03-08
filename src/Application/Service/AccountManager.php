<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Entity\Account\AccountStatus;
use App\Domain\Exception\Account\AccountActionFailedException;
use App\Domain\Exception\Account\AccountAlreadyExistException;
use App\Domain\Exception\Account\AccountNotFoundException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class AccountManager
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    /**
     * @throws AccountActionFailedException
     * @throws AccountNotFoundException
     */
    public function applyAction(string $uuid, string $action): void
    {
        $account = $this->accountRepository->findOneByUuid($uuid);

        if (!$this->accountStateMachine->can($account, $action)) {
            throw new AccountActionFailedException();
        }

        $this->accountStateMachine->apply($account, $action);
    }

    /**
     * @param string[] $roles
     *
     * @throws AccountAlreadyExistException
     */
    public function createProfile(string $email, string $password, array $roles): Account
    {
        try {
            $this->accountRepository->findOneByEmail($email);
            throw new AccountAlreadyExistException();
        } catch (AccountNotFoundException) {
            $account = new Account($email, $roles);
            $account->setPassword($this->userPasswordHasher->hashPassword($account, $password));
            $account->setStatus(status: AccountStatus::VERIFIED);

            $this->accountRepository->persist($account);

            return $account;
        }
    }

    /**
     * @throws AccountNotFoundException
     */
    public function deleteProfile(string $uuid): void
    {
        $account = $this->accountRepository->findOneByUuid($uuid);

        $this->accountRepository->remove($account);
    }

    /**
     * @throws AccountNotFoundException
     */
    public function updatePassword(string $uuid, string $password): void
    {
        $account = $this->accountRepository->findOneByUuid($uuid);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $password));

        $this->accountRepository->persist($account);
    }

    /**
     * @param string[] $roles
     *
     * @throws AccountNotFoundException
     */
    public function updateProfile(string $uuid, ?string $email = null, ?array $roles = null): void
    {
        $account = $this->accountRepository->findOneByUuid($uuid);
        $account->setEmail(email: $email ?? $account->getEmail());
        $account->setRoles(roles: $roles ?? $account->getRoles());

        $this->accountRepository->persist($account);
    }
}
