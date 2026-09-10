<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteAccountByIdCommandHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(DeleteAccountByIdCommand $command): DeleteAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $this->accountRepository->findOneById($accountId)->delete()
            |> $this->accountRepository->save(...);

        return DeleteAccountByIdCommandResult::success();
    }
}
