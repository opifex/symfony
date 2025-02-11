<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\DeleteAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteAccountByIdHandler
{
    public function __construct(private readonly AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(DeleteAccountByIdRequest $message): DeleteAccountByIdResponse
    {
        $this->accountRepository->deleteOneByUuid($message->uuid);

        return DeleteAccountByIdResponse::create();
    }
}
