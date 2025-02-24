<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[AsMonologProcessor]
final class AuthorizationProcessor
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $token = $this->tokenStorage->getToken();

        if ($token instanceof TokenInterface) {
            $record->extra['authorization'] = [
                'user' => $token->getUserIdentifier(),
                'roles' => $token->getRoleNames(),
            ];
        }

        return $record;
    }
}
