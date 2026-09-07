<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Middleware;

use App\Application\Contract\EventMessageBusInterface;
use App\Infrastructure\Messenger\DomainEventCollector;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

#[AsAlias('application_domain_event')]
final readonly class DomainEventMiddleware implements MiddlewareInterface
{
    public function __construct(
        private DomainEventCollector $domainEventCollector,
        private EventMessageBusInterface $eventMessageBus,
    ) {
    }

    /**
     * @throws Throwable
     */
    #[Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            $envelope = $stack->next()->handle($envelope, $stack);
        } catch (Throwable $throwable) {
            $this->domainEventCollector->releaseEvents();
            throw $throwable;
        }

        foreach ($this->domainEventCollector->releaseEvents() as $event) {
            $this->eventMessageBus->publish($event);
        }

        return $envelope;
    }
}
