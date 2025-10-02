<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Application\Contract\AuthenticationRateLimiterInterface;
use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Application\Contract\JwtAccessTokenManagerInterface;
use App\Application\Exception\AuthorizationRequiredException;
use App\Application\Exception\AuthorizationThrottlingException;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthenticationRateLimiterInterface $authenticationRateLimiter,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly JwtAccessTokenManagerInterface $jwtAccessTokenManager,
    ) {
    }

    public function __invoke(SigninIntoAccountRequest $request): SigninIntoAccountResult
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();

        if ($userIdentifier === null) {
            if (!$this->authenticationRateLimiter->isAccepted($request->email)) {
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

        return SigninIntoAccountResult::success($accessToken);
    }
}
