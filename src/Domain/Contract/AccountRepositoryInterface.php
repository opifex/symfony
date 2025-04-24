<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountSearchResult;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;

interface AccountRepositoryInterface
{
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
     * @throws AccountAlreadyExistsException
     */
    public function addOneAccount(string $email, string $password, string $locale): string;

    /**
     * @throws AccountNotFoundException
     */
    public function deleteOneByUuid(string $uuid): void;

    public function checkExistsByEmail(string $email): bool;
}
