<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Domain\Foundation\MessageHandlerResult;

interface CommandMessageBusInterface
{
    public const string NAME = 'command.bus';

    public function dispatch(object $command): MessageHandlerResult;
}
