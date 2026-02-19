<?php

declare(strict_types=1);

namespace App\Application\Command\BlockAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BlockAccountByIdCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(BlockAccountByIdCommand $command): BlockAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $this->accountEntityRepository->findOneById($accountId)
            |> $this->accountStateMachine->block(...)
            |> $this->accountEntityRepository->save(...);

        return BlockAccountByIdCommandResult::success();
    }
}
