<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\IdentityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
final class RequestEventListener
{
    public function __construct(private IdentityManagerInterface $identityManager)
    {
    }

    public function __invoke(RequestEvent $event): void
    {
        $identifier = strval($event->getRequest()->headers->get(key: 'X-Request-Id'));

        if ($this->identityManager->validateIdentifier($identifier)) {
            $this->identityManager->changeIdentifier($identifier);
        }
    }
}
