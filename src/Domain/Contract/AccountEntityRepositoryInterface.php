<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;

interface AccountEntityRepositoryInterface
{
    public function createEntityBuilder(): AccountEntityBuilderInterface;

    public function addOneAccount(AccountEntityInterface $accountEntity): string;

    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult;

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
    public function findStatusByUuid(string $uuid): string;

    /**
     * @throws AccountNotFoundException
     */
    public function updateEmailByUuid(string $uuid, string $email): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updateLocaleByUuid(string $uuid, string $locale): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updateStatusByUuid(string $uuid, string $status): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updatePasswordByUuid(string $uuid, string $password): void;

    /**
     * @throws AccountNotFoundException
     */
    public function deleteOneByUuid(string $uuid): void;

    public function checkExistsByEmail(string $email): bool;
}
