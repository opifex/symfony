<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Application\Service\AccountManager;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Event\Account\AccountCreatedEvent;
use App\Domain\Exception\Account\AccountAlreadyExistException;
use App\Domain\Message\Account\CreateNewAccountCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class CreateNewAccountHandler
{
    public function __construct(
        private AccountManager $accountManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateNewAccountCommand $message): void
    {
        try {
            $account = $this->accountManager->createProfile(
                email: $message->email,
                password: $message->password,
                roles: $message->roles,
            );
            $this->eventDispatcher->dispatch(new AccountCreatedEvent($account));
        } catch (AccountAlreadyExistException $e) {
            throw new ConflictHttpException(
                message: 'Email address is already associated with another account.',
                previous: $e,
            );
        }
    }
}
