<?php

declare(strict_types=1);

namespace App\Infrastructure\EventDispatcher;

use App\Application\Contract\ApplicationEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationEventDispatcher implements ApplicationEventDispatcherInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatch(object $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
