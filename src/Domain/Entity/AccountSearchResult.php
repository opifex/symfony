<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountSearchResult
{
    /**
     * @param AccountCollection $accounts
     * @param int<0, max> $totalResultCount
     */
    public function __construct(
        private readonly AccountCollection $accounts,
        private readonly int $totalResultCount,
    ) {
    }

    public function getAccounts(): AccountCollection
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
