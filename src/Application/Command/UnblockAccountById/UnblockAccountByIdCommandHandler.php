<?php

declare(strict_types=1);

namespace App\Application\Command\UnblockAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UnblockAccountByIdCommandHandler
{
    public function __construct(
        private AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(UnblockAccountByIdCommand $command): UnblockAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $this->accountEntityRepository->findOneById($accountId)->unblock()
            |> $this->accountEntityRepository->save(...);

        return UnblockAccountByIdCommandResult::success();
    }
}
