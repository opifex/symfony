<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use App\Domain\Contract\AccountFactoryInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Event\AccountCreateEvent;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class SignupNewAccountHandler
{
    public function __construct(
        private AccountFactoryInterface $accountFactory,
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
