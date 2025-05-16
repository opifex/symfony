<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Contract\Integration\JwtTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly JwtTokenManagerInterface $jwtTokenManager,
    ) {
    }

    public function __invoke(SigninIntoAccountRequest $message): SigninIntoAccountResult
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();
        $account = $this->accountEntityRepository->findOneByUuid($userIdentifier);

        $accessToken = $this->jwtTokenManager->createAccessToken(
            userIdentifier: $account->getUuid(),
            userRoles: $account->getRoles(),
        );

        return SigninIntoAccountResult::success($accessToken);
    }
}
