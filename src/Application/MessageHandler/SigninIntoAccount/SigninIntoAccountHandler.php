<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Contract\Integration\JwtAccessTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Exception\Authorization\AuthorizationRequiredException;
use App\Domain\Exception\Authorization\AuthorizationThrottlingException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly JwtAccessTokenManagerInterface $jwtAccessTokenManager,
        private readonly RateLimiterFactoryInterface $authenticationLimiter,
    ) {
    }

    public function __invoke(SigninIntoAccountRequest $request): SigninIntoAccountResult
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();

        if (!$userIdentifier) {
            $this->authenticationLimiter->create($request->email)->consume()->isAccepted()
                ? throw AuthorizationRequiredException::create()
                : throw AuthorizationThrottlingException::create();
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
