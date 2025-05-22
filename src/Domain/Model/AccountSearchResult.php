<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountSearchResult
{
    /**
     * @param Account[] $accounts
     * @param int<0, max> $totalResultCount
     */
    public function __construct(
        public readonly array $accounts,
        public readonly int $totalResultCount,
    ) {
    }
}
