<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UnblockAccountById;

use App\Domain\Contract\AccountWorkflowManagerInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Entity\AccountRole;
use App\Domain\Exception\AuthorizationForbiddenException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UnblockAccountByIdHandler
{
    public function __construct(
        private readonly AccountWorkflowManagerInterface $accountWorkflowManager,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(UnblockAccountByIdRequest $message): UnblockAccountByIdResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        $this->accountWorkflowManager->unblock($message->uuid);

        return UnblockAccountByIdResult::success();
    }
}
