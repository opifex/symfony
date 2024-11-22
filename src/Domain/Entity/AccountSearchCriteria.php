<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountSearchCriteria
{
    public const string FIELD_CREATED_AT = 'created_at';
    public const string FIELD_EMAIL = 'email';
    public const string FIELD_STATUS = 'status';
    /** @var string[] */
    public const array SORTING_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_EMAIL,
        self::FIELD_STATUS,
    ];

    public function __construct(
        private readonly ?string $email = null,
        private readonly ?string $status = null,
        private readonly ?SearchSorting $sorting = null,
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

    public function getSorting(): ?SearchSorting
    {
        return $this->sorting;
    }

    public function getPagination(): ?SearchPagination
    {
        return $this->pagination;
    }
}
