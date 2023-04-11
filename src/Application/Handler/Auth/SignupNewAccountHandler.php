<?php

declare(strict_types=1);

namespace App\Application\Handler\Auth;

use App\Application\Service\AccountFactory;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Event\AccountCreateEvent;
use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Message\Auth\SignupNewAccountCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class SignupNewAccountHandler
{
    public function __construct(
        private AccountFactory $accountFactory,
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $message): void
    {
        try {
            $this->accountRepository->findOneByEmail($message->email);
            throw new ConflictHttpException(
                message: 'Email address is already associated with another account.',
            );
        } catch (AccountNotFoundException) {
            $account = $this->accountFactory->createUserAccount($message->email, $message->password);
            $this->accountRepository->persist($account);
            $this->eventDispatcher->dispatch(new AccountCreateEvent($account));
        }
    }
}
