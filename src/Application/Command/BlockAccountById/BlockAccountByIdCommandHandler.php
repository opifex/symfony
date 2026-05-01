<?php

declare(strict_types=1);

namespace App\Application\Command\BlockAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class BlockAccountByIdCommandHandler
{
    public function __construct(
        private AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(BlockAccountByIdCommand $command): BlockAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $this->accountEntityRepository->findOneById($accountId)->block()
            |> $this->accountEntityRepository->save(...);

        return BlockAccountByIdCommandResult::success();
    }
}
