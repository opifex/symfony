<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\MessageIdentifierInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class)]
final class ResponseEventListener
{
    public function __construct(private MessageIdentifierInterface $messageIdentifier)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add([
            'X-Request-Id' => $this->messageIdentifier->identify(),
        ]);
    }
}
