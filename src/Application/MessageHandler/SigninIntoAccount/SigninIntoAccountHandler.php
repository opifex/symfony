<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Contract\JwtTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly JwtTokenManagerInterface $jwtTokenManager,
    ) {
    }

    public function __invoke(SigninIntoAccountRequest $message): SigninIntoAccountResult
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();
        $account = $this->accountRepository->findOneByUuid($userIdentifier);

        $accessToken = $this->jwtTokenManager->createAccessToken(
            userIdentifier: $account->getUuid(),
            userRoles: $account->getRoles(),
        );

        return SigninIntoAccountResult::success($accessToken);
    }
}
