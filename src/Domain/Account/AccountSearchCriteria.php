<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Domain\Foundation\SearchPagination;

class AccountSearchCriteria
{
    public function __construct(
        private readonly ?string $email = null,
        private readonly ?string $status = null,
        private readonly ?SearchPagination $pagination = null,
    ) {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPagination(): ?SearchPagination
    {
        return $this->pagination;
    }
}
