<?php

declare(strict_types=1);

namespace App\Application\Handler\DeleteAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(DeleteAccountByIdCommand $message): void
    {
        $this->accountRepository->delete($message->uuid);
    }
}
