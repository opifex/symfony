<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface CommandMessageBusInterface
{
    public function dispatch(object $command): mixed;
}
