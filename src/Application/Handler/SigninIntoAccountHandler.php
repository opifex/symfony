<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Domain\Message\SigninIntoAccountCommand;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class SigninIntoAccountHandler
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function __invoke(SigninIntoAccountCommand $message): void
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            throw new AccessDeniedHttpException(
                message: 'An authentication exception occurred.',
            );
        }
    }
}
