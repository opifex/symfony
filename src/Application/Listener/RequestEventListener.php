<?php

declare(strict_types=1);

namespace App\Application\Listener;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\Uuid;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
final class RequestEventListener
{
    public function __invoke(RequestEvent $event): void
    {
        $uuid = $event->getRequest()->headers->get(key: 'X-Request-Id');

        try {
            $uuid = is_null($uuid) ? Uuid::v4() : Uuid::fromString($uuid);
            $event->getRequest()->headers->add(['X-Request-Id' => $uuid]);
        } catch (InvalidArgumentException) {
            $event->getRequest()->headers->add(['X-Request-Id' => Uuid::v4()]);
        }
    }
}
