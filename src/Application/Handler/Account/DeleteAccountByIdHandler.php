<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Application\Service\AccountManager;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Message\Account\DeleteAccountByIdCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class DeleteAccountByIdHandler
{
    public function __construct(private AccountManager $accountManager)
    {
    }

    public function __invoke(DeleteAccountByIdCommand $message): void
    {
        try {
            $this->accountManager->deleteProfile($message->uuid);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }
}
