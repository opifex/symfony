<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

final readonly class SearchResult
{
    /**
     * @param object[] $items
     */
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $pageNumber,
        public int $pageSize,
    ) {
    }
}
