<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Application\Service\AccountManager;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Message\Account\UpdateAccountByIdCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class UpdateAccountByIdHandler
{
    public function __construct(private AccountManager $accountManager)
    {
    }

    public function __invoke(UpdateAccountByIdCommand $message): void
    {
        try {
            $this->accountManager->updateProfile($message->uuid, $message->email, $message->roles);

            if ($message->password !== null) {
                $this->accountManager->updatePassword($message->uuid, $message->password);
            }
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }
}
