<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\MessageBus;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

#[Lazy]
final readonly class EventMessageBus
{
    public function __construct(
        #[Autowire(service: 'event.bus')]
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function publish(object $event): void
    {
        try {
            $this->messageBus->dispatch($event);
        } catch (NoHandlerForMessageException) {
            return;
        }
    }
}
