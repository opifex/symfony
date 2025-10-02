<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Security\Role;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountByIdHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(GetAccountByIdRequest $request): GetAccountByIdResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: Role::Admin);

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        return GetAccountByIdResult::success($account);
    }
}
