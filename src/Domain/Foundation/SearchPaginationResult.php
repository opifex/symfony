<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

class SearchPaginationResult
{
    /**
     * @param object[] $resultItemsList
     */
    public function __construct(
        public readonly array $resultItemsList,
        public readonly int $totalResultsCount,
        public readonly int $currentPageNumber,
        public readonly int $itemsPerPageAmount,
    ) {
    }
}
