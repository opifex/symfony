<?php

declare(strict_types=1);

namespace App\Domain\Criteria;

class AccountSearchCriteria
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $status = null,
        public readonly ?string $sort = null,
        public readonly ?string $order = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
