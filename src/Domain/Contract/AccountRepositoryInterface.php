<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Criteria\AccountSearchCriteria;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountNotFoundException;

interface AccountRepositoryInterface
{
    /**
     * @return Account[]
     */
    public function findByCriteria(AccountSearchCriteria $criteria): iterable;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneByEmail(string $email): Account;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneByUuid(string $uuid): Account;

    public function persist(Account $account): void;

    public function remove(Account $account): void;
}
