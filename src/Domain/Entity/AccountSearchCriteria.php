<?php

declare(strict_types=1);

namespace App\Domain\Entity;

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
        public readonly ?string $email = null,
        public readonly ?string $status = null,
        public readonly ?SearchSorting $sorting = null,
        public readonly ?SearchPagination $pagination = null,
    ) {
    }
}
