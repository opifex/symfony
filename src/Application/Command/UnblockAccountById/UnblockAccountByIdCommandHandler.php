<?php

declare(strict_types=1);

namespace App\Application\Command\UnblockAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UnblockAccountByIdCommandHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(UnblockAccountByIdCommand $command): UnblockAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $this->accountRepository->findOneById($accountId)->unblock()
            |> $this->accountRepository->save(...);

        return UnblockAccountByIdCommandResult::success();
    }
}
