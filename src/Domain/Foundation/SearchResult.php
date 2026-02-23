<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

class SearchResult
{
    /**
     * @param object[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $totalCount,
        public readonly int $pageNumber,
        public readonly int $pageSize,
    ) {
    }
}
