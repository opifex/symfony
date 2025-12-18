<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\BlockAccountById;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Account\AccountRole;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BlockAccountByIdHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(BlockAccountByIdRequest $request): BlockAccountByIdResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::Admin);

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        $account = $this->accountStateMachine->block($account);
        $this->accountEntityRepository->save($account);

        return BlockAccountByIdResult::success();
    }
}
