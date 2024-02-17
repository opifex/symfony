<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    public function __invoke(CreateNewAccountCommand $message): CreateNewAccountResponse
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(user: Account::class);
        $password = $passwordHasher->hash($message->password);

        $account = AccountFactory::createCustomAccount($message->email, $password, $message->locale, $message->roles);
        $this->accountRepository->insertOneAccount($account);

        $this->accountStateMachine->apply($account, transitionName: AccountAction::REGISTER);

        $account = $this->accountRepository->findOneByUuid(uuid: $account->getUuid());

        return new CreateNewAccountResponse($account);
    }
}
