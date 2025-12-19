<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Domain\Foundation\MessageHandlerResult;

interface QueryMessageBusInterface
{
    public function ask(object $query): MessageHandlerResult;
}
