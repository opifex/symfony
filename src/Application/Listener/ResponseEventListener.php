<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\RequestIdStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class)]
final class ResponseEventListener
{
    public function __construct(private RequestIdStorageInterface $requestIdStorage)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        $requestId = $this->requestIdStorage->getRequestId();

        $event->getResponse()->headers->set(key: 'X-Request-Id', values: $requestId);
    }
}
