<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\Role;
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
        if (!$this->authorizationTokenManager->checkPermission(access: Role::Admin->toString())) {
            throw AuthorizationForbiddenException::create();
        }

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        return GetAccountByIdResult::success($account);
    }
}
