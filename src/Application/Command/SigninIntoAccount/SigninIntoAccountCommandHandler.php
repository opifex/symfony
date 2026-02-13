<?php

declare(strict_types=1);

namespace App\Application\Command\SigninIntoAccount;

use App\Application\Contract\AuthenticationRateLimiterInterface;
use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Contract\JwtAccessTokenManagerInterface;
use App\Application\Exception\AuthorizationRequiredException;
use App\Application\Exception\AuthorizationThrottlingException;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthenticationRateLimiterInterface $authenticationRateLimiter,
        private readonly AuthorizationTokenStorageInterface $authorizationTokenStorage,
        private readonly JwtAccessTokenManagerInterface $jwtAccessTokenManager,
    ) {
    }

    public function __invoke(SigninIntoAccountCommand $command): SigninIntoAccountCommandResult
    {
        $userIdentifier = $this->authorizationTokenStorage->getUserIdentifier();

        if ($userIdentifier === null) {
            if (!$this->authenticationRateLimiter->isAccepted($command->email)) {
                throw AuthorizationThrottlingException::create();
            }

            throw AuthorizationRequiredException::create();
        }

        $account = $this->accountEntityRepository->findOneById($userIdentifier)
            ?? throw AccountNotFoundException::create();

        $accessToken = $this->jwtAccessTokenManager->createAccessToken(
            userIdentifier: $account->getId()->toString(),
            userRoles: $account->getRoles()->toArray(),
        );

        return SigninIntoAccountCommandResult::success($accessToken);
    }
}
