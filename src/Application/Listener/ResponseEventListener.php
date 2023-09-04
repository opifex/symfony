<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\RequestIdentifierInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class)]
final class ResponseEventListener
{
    public function __construct(private RequestIdentifierInterface $requestIdentifier)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        $identifier = $this->requestIdentifier->getIdentifier();

        $event->getResponse()->headers->set(key: 'X-Request-Id', values: $identifier);
    }
}
