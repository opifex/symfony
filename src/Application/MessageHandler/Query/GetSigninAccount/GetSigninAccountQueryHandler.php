<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Query\GetSigninAccount;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Application\Exception\AuthorizationRequiredException;
use App\Domain\Account\AccountRole;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetSigninAccountQuery $request): GetSigninAccountQueryResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::User);

        $userIdentifier = $this->authorizationTokenManager->getUserIdentifier()
            ?? throw AuthorizationRequiredException::create();

        $account = $this->accountEntityRepository->findOneById($userIdentifier)
            ?? throw AccountNotFoundException::create();

        return GetSigninAccountQueryResult::success($account);
    }
}
