<?php

declare(strict_types=1);

namespace App\Application\Query\GetSigninAccount;

use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenStorageInterface $authorizationTokenStorage,
    ) {
    }

    public function __invoke(GetSigninAccountQuery $query): GetSigninAccountQueryResult
    {
        $userIdentifier = $this->authorizationTokenStorage->getUserIdentifier();

        $accountId = AccountIdentifier::fromString($userIdentifier);

        $account = $this->accountEntityRepository->findOneById($accountId);

        return GetSigninAccountQueryResult::success($account);
    }
}
