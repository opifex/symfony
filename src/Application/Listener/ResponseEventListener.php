<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\IdentityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: ResponseEvent::class)]
final class ResponseEventListener
{
    public function __construct(private IdentityManagerInterface $identityManager)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add([
            'X-Request-Id' => $this->identityManager->extractIdentifier(),
        ]);
    }
}
