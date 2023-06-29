<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountSearchCriteria;
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

    public function persist(Account $account): void;

    public function remove(Account $account): void;
}
