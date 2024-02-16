<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private WorkflowInterface $accountStateMachine,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $message): SignupNewAccountResponse
    {
        $account = AccountFactory::createUserAccount($message->email, $message->locale);
        $this->accountRepository->insertOneAccount($account);

        $password = $this->userPasswordHasher->hashPassword($account, $message->password);
        $this->accountRepository->updatePasswordByUuid($account->getUuid(), $password);

        $this->accountStateMachine->apply($account, transitionName: AccountAction::REGISTER);

        return new SignupNewAccountResponse();
    }
}
