<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Application\Service\AccountManager;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountAction;
use App\Domain\Event\Account\AccountCreatedEvent;
use App\Domain\Exception\Account\AccountActionFailedException;
use App\Domain\Exception\Account\AccountAlreadyExistException;
use App\Domain\Exception\Account\AccountNotFoundException;
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

    /**
     * @throws AccountNotFoundException
     * @throws AccountActionFailedException
     */
    public function __invoke(CreateNewAccountCommand $message): void
    {
        try {
            $account = $this->accountManager->createProfile($message->email, $message->password, $message->roles);
            $this->accountManager->applyAction($account->getUuid(), action: AccountAction::VERIFY);
            $this->eventDispatcher->dispatch(new AccountCreatedEvent($account));
        } catch (AccountAlreadyExistException $e) {
            throw new ConflictHttpException(
                message: 'Email address is already associated with another account.',
                previous: $e,
            );
        }
    }
}
