<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\EventListener;

use App\Infrastructure\Observability\CorrelationIdProvider;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class, priority: -100)]
final readonly class CorrelationIdEventListener
{
    public function __construct(
        private CorrelationIdProvider $correlationIdProvider,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $headers = $event->getResponse()->headers;
        $correlationId = $this->correlationIdProvider->getCorrelationId();

        $headers->set(key: 'X-Correlation-Id', values: $correlationId);
    }
}
