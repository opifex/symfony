<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Event\AccountCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class SignupNewAccountHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $message): void
    {
        $account = AccountFactory::createUserAccount($message->email, $message->locale);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $message->password));

        $this->accountRepository->insert($account);

        $this->eventDispatcher->dispatch(new AccountCreatedEvent($account));
    }
}
