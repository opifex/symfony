<?php

declare(strict_types=1);

namespace App\Application\Command\UnblockAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UnblockAccountByIdCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(UnblockAccountByIdCommand $command): UnblockAccountByIdCommandResult
    {
        $this->accountEntityRepository->findOneById($command->id)
            |> $this->accountStateMachine->unblock(...)
            |> $this->accountEntityRepository->save(...);

        return UnblockAccountByIdCommandResult::success();
    }
}
