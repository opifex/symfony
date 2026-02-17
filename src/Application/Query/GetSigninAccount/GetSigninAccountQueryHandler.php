<?php

declare(strict_types=1);

namespace App\Application\Query\GetSigninAccount;

use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Exception\AuthorizationRequiredException;
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
        $userIdentifier = $this->authorizationTokenStorage->getUserIdentifier()
            ?? throw AuthorizationRequiredException::create();

        $account = $this->accountEntityRepository->findOneById($userIdentifier);

        return GetSigninAccountQueryResult::success($account);
    }
}
