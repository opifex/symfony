<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use SensitiveParameter;

interface AccountRepositoryInterface
{
    public function findByCriteria(AccountSearchCriteria $criteria): AccountCollection;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneByEmail(string $email): Account;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneByUuid(string $uuid): Account;

    /**
     * @throws AccountNotFoundException
     */
    public function updateEmailByUuid(string $uuid, string $email): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updatePasswordByUuid(string $uuid, #[SensitiveParameter] string $password): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updateStatusByUuid(string $uuid, string $status): void;

    /**
     * @param string[] $roles
     * @throws AccountNotFoundException
     */
    public function updateRolesByUuid(string $uuid, array $roles): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updateLocaleByUuid(string $uuid, string $locale): void;

    /**
     * @throws AccountAlreadyExistsException
     */
    public function insertOneAccount(Account $account): void;

    /**
     * @throws AccountNotFoundException
     */
    public function deleteOneByUuid(string $uuid): void;
}
