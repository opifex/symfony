<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class SearchPagination
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
    ) {
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
