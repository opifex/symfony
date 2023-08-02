<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;

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
    public function deleteByUuid(string $uuid): void;

    /**
     * @throws AccountAlreadyExistsException
     */
    public function saveNewAccount(Account $account): void;
}
