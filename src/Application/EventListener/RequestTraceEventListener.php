<?php

declare(strict_types=1);

namespace App\Application\EventListener;

use App\Application\Contract\RequestTraceManagerInterface;
use App\Domain\Foundation\HttpSpecification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class RequestTraceEventListener
{
    public function __construct(
        private readonly RequestTraceManagerInterface $requestTraceManager,
    ) {
    }

    #[AsEventListener(event: ResponseEvent::class, priority: -100)]
    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $event->getResponse()->headers->set(
            key: HttpSpecification::HEADER_X_REQUEST_ID,
            values: $this->requestTraceManager->getTraceId(),
        );
    }
}
