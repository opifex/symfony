<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\UuidV4;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
final class RequestListener
{
    public function __invoke(RequestEvent $event): void
    {
        $requestId = $event->getRequest()->headers->get(key: 'X-Request-Id');

        try {
            $event->getRequest()->headers->add(['X-Request-Id' => new UuidV4($requestId)]);
        } catch (InvalidArgumentException) {
            $event->getRequest()->headers->add(['X-Request-Id' => new UuidV4()]);
        }
    }
}
