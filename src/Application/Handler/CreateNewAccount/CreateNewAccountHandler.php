<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\AccountAction;
use App\Domain\Event\AccountCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateNewAccountHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
        private UserPasswordHasherInterface $userPasswordHasher,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    public function __invoke(CreateNewAccountCommand $message): void
    {
        $account = AccountFactory::createCustomAccount($message->email, $message->locale, $message->roles);
        $account->setPassword($this->userPasswordHasher->hashPassword($account, $message->password));
        $this->accountStateMachine->apply($account, transitionName: AccountAction::VERIFY);

        $this->accountRepository->insert($account);

        $this->eventDispatcher->dispatch(new AccountCreatedEvent($account));
    }
}
