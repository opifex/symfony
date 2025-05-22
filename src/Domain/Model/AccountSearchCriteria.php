<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountSearchCriteria
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?string $status = null,
        public readonly ?SearchPagination $pagination = null,
    ) {
    }
}
