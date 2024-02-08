<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class SearchSorting
{
    public function __construct(
        public readonly string $field,
        public readonly SortingOrder $order,
    ) {
    }
}
