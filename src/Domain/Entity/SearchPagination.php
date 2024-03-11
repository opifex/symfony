<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class SearchPagination
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
