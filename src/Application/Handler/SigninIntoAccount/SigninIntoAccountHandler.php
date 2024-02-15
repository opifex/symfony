<?php

declare(strict_types=1);

namespace App\Application\Handler\SigninIntoAccount;

use App\Domain\Entity\AuthorizationToken;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class SigninIntoAccountHandler
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(SigninIntoAccountCommand $message): SigninIntoAccountResponse
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof AuthorizationToken) {
            throw new AccessDeniedException(
                message: 'An authentication exception occurred.',
            );
        }

        return new SigninIntoAccountResponse($token);
    }
}
