<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractController
{
    public function __construct(
        protected MessageBusInterface $commandBus,
        protected MessageBusInterface $queryBus,
    ) {
    }
}
