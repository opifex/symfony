<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\IdentityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\Uuid;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
final class RequestEventListener
{
    public function __construct(private IdentityManagerInterface $identityManager)
    {
    }

    public function __invoke(RequestEvent $event): void
    {
        $identityHeader = $event->getRequest()->headers->get(key: 'X-Request-Id') ?? '';
        $requestIdentifier = Uuid::isValid($identityHeader) ? Uuid::fromString($identityHeader) : Uuid::v4();

        $this->identityManager->setIdentifier($requestIdentifier->toRfc4122());
    }
}
