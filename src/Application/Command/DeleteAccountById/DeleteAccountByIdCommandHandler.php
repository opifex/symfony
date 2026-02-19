<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteAccountByIdCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(DeleteAccountByIdCommand $command): DeleteAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $this->accountEntityRepository->findOneById($accountId)
            |> $this->accountEntityRepository->delete(...);

        return DeleteAccountByIdCommandResult::success();
    }
}
