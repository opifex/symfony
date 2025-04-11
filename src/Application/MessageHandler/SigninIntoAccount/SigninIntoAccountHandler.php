<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\AccountTokenStorageInterface;
use App\Domain\Contract\JwtTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(
        private readonly AccountTokenStorageInterface $accountTokenStorage,
        private readonly JwtTokenManagerInterface $jwtTokenManager,
    ) {
    }

    public function __invoke(SigninIntoAccountRequest $message): SigninIntoAccountResponse
    {
        $account = $this->accountTokenStorage->getAccount();
        $accessToken = $this->jwtTokenManager->createAccessToken(
            userIdentifier: $account->getUuid(),
            userRoles: $account->getRoles(),
        );

        return SigninIntoAccountResponse::create($accessToken);
    }
}
