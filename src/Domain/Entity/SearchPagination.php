<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class SearchPagination
{
    public function __construct(
        private readonly int $page,
        private readonly int $limit,
    ) {
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
