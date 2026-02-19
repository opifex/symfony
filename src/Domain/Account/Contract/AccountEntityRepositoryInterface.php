<?php

declare(strict_types=1);

namespace App\Domain\Account\Contract;

use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\AccountSearchResult;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Foundation\ValueObject\EmailAddress;

interface AccountEntityRepositoryInterface
{
    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneById(AccountIdentifier $id): Account;

    /**
     * @throws AccountNotFoundException
     */
    public function findOneByEmail(EmailAddress $email): Account;

    /**
     * @throws AccountAlreadyExistsException
     */
    public function ensureEmailIsAvailable(EmailAddress $email): void;

    public function delete(Account $account): void;

    public function save(Account $account): Account;
}
