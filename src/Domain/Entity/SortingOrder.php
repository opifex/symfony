<?php

declare(strict_types=1);

namespace App\Domain\Entity;

enum SortingOrder: string
{
    case ASC = 'asc';

    case DESC = 'desc';

    public const LIST = [
        self::ASC->value,
        self::DESC->value,
    ];
}
