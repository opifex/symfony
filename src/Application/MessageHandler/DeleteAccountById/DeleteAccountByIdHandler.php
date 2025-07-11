<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\Role;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteAccountByIdHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(DeleteAccountByIdRequest $request): DeleteAccountByIdResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: Role::Admin->toString())) {
            throw AuthorizationForbiddenException::create();
        }

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        $this->accountEntityRepository->delete($account);

        return DeleteAccountByIdResult::success();
    }
}
