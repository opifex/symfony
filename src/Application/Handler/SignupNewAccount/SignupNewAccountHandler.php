<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $message): SignupNewAccountResponse
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(user: Account::class);
        $password = $passwordHasher->hash($message->password);

        $account = AccountFactory::createUserAccount($message->email, $password, $message->locale);
        $this->accountRepository->insertOneAccount($account);

        $this->accountStateMachine->apply($account, transitionName: AccountAction::REGISTER);

        return new SignupNewAccountResponse();
    }
}
