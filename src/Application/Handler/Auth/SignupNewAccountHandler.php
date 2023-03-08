<?php

declare(strict_types=1);

namespace App\Application\Handler\Auth;

use App\Application\Service\AccountManager;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountRole;
use App\Domain\Event\Account\AccountCreatedEvent;
use App\Domain\Exception\Account\AccountAlreadyExistException;
use App\Domain\Message\Auth\SignupNewAccountCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class SignupNewAccountHandler
{
    public function __construct(
        private AccountManager $accountManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $message): void
    {
        try {
            $account = $this->accountManager->createProfile(
                email: $message->email,
                password: $message->password,
                roles: [AccountRole::ROLE_USER],
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
