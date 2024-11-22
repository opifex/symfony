<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class SearchSorting
{
    public function __construct(
        private readonly string $field,
        private readonly SortingOrder $order,
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOrder(): SortingOrder
    {
        return $this->order;
    }
}
