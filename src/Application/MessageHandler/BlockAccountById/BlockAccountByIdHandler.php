<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\BlockAccountById;

use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\AccountIdentifier;
use App\Domain\Model\AccountRole;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BlockAccountByIdHandler
{
    public function __construct(
        private readonly AccountWorkflowManagerInterface $accountWorkflowManager,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(BlockAccountByIdRequest $message): BlockAccountByIdResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        $accountIdentifier = new AccountIdentifier($message->id);
        $this->accountWorkflowManager->block($accountIdentifier);

        return BlockAccountByIdResult::success();
    }
}
