<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Query\QueryInterface;

interface QueryMessageBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
