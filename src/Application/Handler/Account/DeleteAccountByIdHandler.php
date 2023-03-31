<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Message\Account\DeleteAccountByIdCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class DeleteAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(DeleteAccountByIdCommand $message): void
    {
        try {
            $account = $this->accountRepository->findOneByUuid($message->uuid);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }

        $this->accountRepository->remove($account);
    }
}
