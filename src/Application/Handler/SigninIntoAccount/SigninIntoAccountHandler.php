<?php

declare(strict_types=1);

namespace App\Application\Handler\SigninIntoAccount;

use App\Domain\Contract\AuthorizationTokenInterface;
use App\Domain\Event\AccountAuthenticatedEvent;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class SigninIntoAccountHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(SigninIntoAccountCommand $message): void
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof AuthorizationTokenInterface) {
            throw new AccessDeniedException(
                message: 'An authentication exception occurred.',
            );
        }

        $this->eventDispatcher->dispatch(new AccountAuthenticatedEvent($token));
    }
}
