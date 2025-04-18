<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountHandler
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetSigninAccountRequest $message): GetSigninAccountResponse
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();
        $account = $this->accountRepository->findOneByUuid($userIdentifier);

        return GetSigninAccountResponse::create($account);
    }
}
