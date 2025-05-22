<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;

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
     * @throws AccountNotFoundException
     */
    public function getStatusById(string $id): string;

    /**
     * @throws AccountNotFoundException
     */
    public function updateEmailById(string $id, string $email): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updateLocaleById(string $id, string $locale): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updateStatusById(string $id, string $status): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updatePasswordById(string $id, string $password): void;

    /**
     * @throws AccountNotFoundException
     */
    public function deleteById(string $id): void;

    public function checkEmailExists(string $email): bool;

    public function save(AccountEntityInterface $entity): string;
}
