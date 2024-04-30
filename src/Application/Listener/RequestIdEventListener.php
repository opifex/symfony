<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\RequestIdGeneratorInterface;
use App\Domain\Contract\RequestIdStorageInterface;
use App\Domain\Entity\HttpSpecification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class RequestIdEventListener
{
    public function __construct(
        private RequestIdGeneratorInterface $requestIdGenerator,
        private RequestIdStorageInterface $requestIdStorage,
    ) {
    }

    #[AsEventListener(event: RequestEvent::class, priority: 100)]
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $requestId = $this->requestIdGenerator->generate();
        $this->requestIdStorage->setRequestId($requestId);
    }

    #[AsEventListener(event: ResponseEvent::class, priority: -100)]
    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $requestId = $this->requestIdStorage->getRequestId();

        if ($requestId !== null) {
            $event->getResponse()->headers->set(
                key: HttpSpecification::HEADER_X_REQUEST_ID,
                values: $requestId,
            );
        }
    }
}
