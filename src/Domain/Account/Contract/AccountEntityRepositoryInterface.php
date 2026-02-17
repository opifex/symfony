<?php

declare(strict_types=1);

namespace App\Domain\Account\Contract;

use App\Domain\Account\Account;
use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\AccountSearchResult;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;

interface AccountEntityRepositoryInterface
{
    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneById(string $id): Account;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneByEmail(string $email): Account;

    /**
     * @throws AccountAlreadyExistsException
     */
    public function ensureEmailIsAvailable(string $email): void;

    public function delete(Account $account): void;

    public function save(Account $account): Account;
}
