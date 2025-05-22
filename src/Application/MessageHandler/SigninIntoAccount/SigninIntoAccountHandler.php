<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Contract\Integration\JwtTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;
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

        if (!$account instanceof Account) {
            throw AccountNotFoundException::create();
        }

        $accessToken = $this->jwtTokenManager->createAccessToken(
            userIdentifier: $account->id,
            userRoles: $account->roles,
        );

        return SigninIntoAccountResult::success($accessToken);
    }
}
