<?php

declare(strict_types=1);

namespace App\Application\Command\SignoutFromAccount;

use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Contract\JwtAccessTokenRevokerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SignoutFromAccountCommandHandler
{
    public function __construct(
        private AuthorizationTokenStorageInterface $authorizationTokenStorage,
        private JwtAccessTokenRevokerInterface $jwtAccessTokenRevoker,
    ) {
    }

    public function __invoke(SignoutFromAccountCommand $command): SignoutFromAccountCommandResult
    {
        $this->jwtAccessTokenRevoker->revoke(
            tokenIdentifier: $this->authorizationTokenStorage->getTokenIdentifier(),
            expiresAt: $this->authorizationTokenStorage->getTokenExpiresAt(),
        );

        return SignoutFromAccountCommandResult::success();
    }
}
