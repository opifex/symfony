<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

class SearchPaginationResult
{
    /**
     * @param object[] $resultItems
     */
    public function __construct(
        private readonly array $resultItems,
        private readonly int $totalResultsCount,
        private readonly int $currentPageNumber,
        private readonly int $itemsPerPageAmount,
    ) {
    }

    /**
     * @return object[]
     */
    public function getResultItems(): array
    {
        return $this->resultItems;
    }

    public function getTotalResultsCount(): int
    {
        return $this->totalResultsCount;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    public function getItemsPerPageAmount(): int
    {
        return $this->itemsPerPageAmount;
    }
}
