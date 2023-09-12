<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountSearchCriteria
{
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_EMAIL = 'email';
    public const FIELD_STATUS = 'status';
    public const SORTING_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_EMAIL,
        self::FIELD_STATUS,
    ];

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
