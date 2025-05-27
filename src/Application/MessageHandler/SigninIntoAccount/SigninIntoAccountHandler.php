<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Contract\Integration\JwtTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
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

    public function __invoke(SigninIntoAccountRequest $request): SigninIntoAccountResult
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();

        $accountIdentifier = new AccountIdentifier($userIdentifier);
        $account = $this->accountEntityRepository->findOneByid($accountIdentifier);

        if (!$account instanceof Account) {
            throw AccountNotFoundException::create();
        }

        $accessToken = $this->jwtTokenManager->createAccessToken(
            userIdentifier: $account->getId()->toString(),
            userRoles: $account->getRoles(),
        );

        return SigninIntoAccountResult::success($accessToken);
    }
}
