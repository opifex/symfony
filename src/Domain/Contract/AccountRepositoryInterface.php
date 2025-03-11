<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountSearchResult;
use App\Domain\Entity\AccountStatus;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use SensitiveParameter;

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
    public function findStatusByUuid(string $uuid): AccountStatus;

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
    public function updateStatusByUuid(string $uuid, AccountStatus $status): void;

    /**
     * @throws AccountNotFoundException
     */
    public function updatePasswordByUuid(string $uuid, #[SensitiveParameter] string $password): void;

    /**
     * @throws AccountAlreadyExistsException
     */
    public function addOneAccount(string $email, #[SensitiveParameter] string $password): string;

    /**
     * @throws AccountNotFoundException
     */
    public function deleteOneByUuid(string $uuid): void;

    public function checkExistsByEmail(string $email): bool;
}
