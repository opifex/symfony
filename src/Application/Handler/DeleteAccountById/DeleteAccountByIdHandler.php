<?php

declare(strict_types=1);

namespace App\Application\Handler\DeleteAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(DeleteAccountByIdCommand $message): void
    {
        try {
            $this->accountRepository->delete($message->uuid);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }
}
