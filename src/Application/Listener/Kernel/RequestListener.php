<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\UuidV4;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
class RequestListener
{
    public function __invoke(RequestEvent $event): void
    {
        $headers = $event->getRequest()->headers;

        try {
            $headers->add(['X-Request-Id' => new UuidV4($headers->get(key: 'X-Request-Id'))]);
        } catch (InvalidArgumentException) {
            $headers->add(['X-Request-Id' => new UuidV4()]);
        }
    }
}
