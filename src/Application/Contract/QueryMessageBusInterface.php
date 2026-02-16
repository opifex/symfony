<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface QueryMessageBusInterface
{
    public function ask(object $query): mixed;
}
