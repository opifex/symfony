<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Domain\Foundation\MessageHandlerResult;

interface CommandMessageBusInterface
{
    public function dispatch(object $command): MessageHandlerResult;
}
