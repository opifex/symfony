<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class)]
final class ResponseEventListener
{
    public function __invoke(ResponseEvent $event): void
    {
        $uuid = $event->getRequest()->headers->get(key: 'X-Request-Id');
        $event->getResponse()->headers->add(['X-Request-Id' => $uuid]);
    }
}
