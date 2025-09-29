<?php

declare(strict_types=1);

namespace App\Domain\Account\Contract;

use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\AccountSearchResult;

interface AccountEntityRepositoryInterface
{
    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult;

    public function findOneById(string $id): ?Account;

    public function findOneByEmail(string $email): ?Account;

    public function delete(Account $account): void;

    public function save(Account $account): AccountIdentifier;
}
