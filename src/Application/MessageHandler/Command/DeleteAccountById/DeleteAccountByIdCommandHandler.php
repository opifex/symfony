<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\DeleteAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteAccountByIdCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(DeleteAccountByIdCommand $request): DeleteAccountByIdCommandResult
    {
        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        $this->accountEntityRepository->delete($account);

        return DeleteAccountByIdCommandResult::success();
    }
}
