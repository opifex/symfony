<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\Command\CommandInterface;

interface CommandMessageBusInterface
{
    public function dispatch(CommandInterface $command): mixed;
}
