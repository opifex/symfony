<?php

declare(strict_types=1);

namespace App\Domain\Contract\Account;

use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;

interface AccountEntityRepositoryInterface
{
    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult;

    public function findOneById(string $id): ?Account;

    public function findOneByEmail(string $email): ?Account;

    public function delete(Account $account): void;

    public function save(Account $account): AccountIdentifier;
}
