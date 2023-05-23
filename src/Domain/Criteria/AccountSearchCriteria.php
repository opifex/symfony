<?php

declare(strict_types=1);

namespace App\Domain\Criteria;

use App\Domain\Entity\SortingOrder;

class AccountSearchCriteria
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $status = null,
        public readonly ?string $sort = null,
        public readonly ?SortingOrder $order = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
