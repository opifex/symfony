<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\EventListener;

use App\Infrastructure\Observability\CorrelationIdProvider;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class CorrelationIdEventListener
{
    public function __construct(
        private readonly CorrelationIdProvider $correlationIdProvider,
    ) {
    }

    #[AsEventListener(event: ResponseEvent::class, priority: -100)]
    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $event->getResponse()->headers->set(
            key: $this->correlationIdProvider->getHttpHeaderName(),
            values: $this->correlationIdProvider->getCorrelationId(),
        );
    }
}
