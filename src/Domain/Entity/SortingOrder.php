<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class SortingOrder
{
    public const ASC = 'asc';
    public const DESC = 'desc';
    public const LIST = [
        self::ASC,
        self::DESC,
    ];
}
