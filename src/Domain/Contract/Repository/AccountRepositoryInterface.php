<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\Entity\Account\Account;
use App\Domain\Exception\AccountNotFoundException;

interface AccountRepositoryInterface
{
    /**
     * @param array&array<string, mixed> $criteria
     * @param int $limit
     * @param int $offset
     *
     * @return Account[]
     */
    public function findByCriteria(array $criteria, int $limit, int $offset): iterable;

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
