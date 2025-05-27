<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\AccountIdentifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetSigninAccountRequest $request): GetSigninAccountResult
    {
        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier();

        $accountId = AccountIdentifier::fromString($userIdentifier);
        $account = $this->accountEntityRepository->findOneById($accountId)
            ?? throw AccountNotFoundException::create();

        return GetSigninAccountResult::success($account);
    }
}
