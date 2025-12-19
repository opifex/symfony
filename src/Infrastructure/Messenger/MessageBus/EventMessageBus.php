<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\MessageBus;

use App\Application\Contract\EventMessageBusInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[Lazy]
final class EventMessageBus implements EventMessageBusInterface
{
    public function __construct(
        #[Autowire(service: 'event.bus')]
        protected readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Override]
    public function publish(object $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
