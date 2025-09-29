<?php

declare(strict_types=1);

namespace App\Domain\Account;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountSearchResult
{
    /**
     * @param Account[] $accounts
     * @param int<0, max> $totalResultCount
     */
    public function __construct(
        private readonly array $accounts,
        private readonly int $totalResultCount,
    ) {
    }

    /**
     * @return Account[]
     */
    public function getAccounts(): array
    {
        return $this->accounts;
    }

    /**
     * @return int<0, max>
     */
    public function getTotalResultCount(): int
    {
        return $this->totalResultCount;
    }
}
