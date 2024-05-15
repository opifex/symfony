<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(DeleteAccountByIdCommand $message): DeleteAccountByIdResponse
    {
        $this->accountRepository->deleteOneByUuid($message->uuid);

        return new DeleteAccountByIdResponse();
    }
}
