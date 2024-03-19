<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\RequestIdStorageInterface;
use App\Domain\Entity\HttpSpecification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class)]
final class KernelResponseEventListener
{
    public function __construct(private RequestIdStorageInterface $requestIdStorage)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        $event->getResponse()->headers->set(
            key: HttpSpecification::HEADER_X_REQUEST_ID,
            values: $this->requestIdStorage->getRequestId(),
        );
    }
}
