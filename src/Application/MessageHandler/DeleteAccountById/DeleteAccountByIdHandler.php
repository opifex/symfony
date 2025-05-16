<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\AccountRole;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteAccountByIdHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(DeleteAccountByIdRequest $message): DeleteAccountByIdResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        $this->accountEntityRepository->deleteByUuid($message->uuid);

        return DeleteAccountByIdResult::success();
    }
}
