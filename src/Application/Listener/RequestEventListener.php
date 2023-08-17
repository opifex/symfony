<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\MessageIdentifierInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
final class RequestEventListener
{
    public function __construct(private MessageIdentifierInterface $messageIdentifier)
    {
    }

    public function __invoke(RequestEvent $event): void
    {
        $identifier = $event->getRequest()->headers->get(key: 'X-Request-Id') ?? '';

        if ($this->messageIdentifier->validate($identifier)) {
            $this->messageIdentifier->replace($identifier);
        }
    }
}
