<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class, priority: -100)]
final readonly class SecurityHeadersEventListener
{
    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $headers = $event->getResponse()->headers;
        $headers->set(key: 'X-Content-Type-Options', values: 'nosniff');
        $headers->set(key: 'X-Frame-Options', values: 'DENY');
    }
}
