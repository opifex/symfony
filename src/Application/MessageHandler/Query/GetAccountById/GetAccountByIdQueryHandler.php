<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Query\GetAccountById;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\AccountRole;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountByIdQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetAccountByIdQuery $request): GetAccountByIdQueryResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::Admin);

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        return GetAccountByIdQueryResult::success($account);
    }
}
