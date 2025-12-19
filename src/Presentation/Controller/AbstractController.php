<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Contract\CommandMessageBusInterface;
use App\Application\Contract\QueryMessageBusInterface;

abstract class AbstractController
{
    public function __construct(
        protected readonly CommandMessageBusInterface $commandMessageBus,
        protected readonly QueryMessageBusInterface $queryMessageBus,
    ) {
    }
}
