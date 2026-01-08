<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\UnblockAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UnblockAccountByIdCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(UnblockAccountByIdCommand $request): UnblockAccountByIdCommandResult
    {
        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        $account = $this->accountStateMachine->unblock($account);
        $this->accountEntityRepository->save($account);

        return UnblockAccountByIdCommandResult::success();
    }
}
