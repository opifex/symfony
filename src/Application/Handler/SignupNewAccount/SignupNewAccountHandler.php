<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private AccountPasswordHasherInterface $accountPasswordHasher,
        private AccountRepositoryInterface $accountRepository,
        private AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $message): SignupNewAccountResponse
    {
        $password = $this->accountPasswordHasher->hash($message->password);
        $account = AccountFactory::createUserAccount($message->email, $password, $message->locale);

        $this->accountRepository->addOneAccount($account);
        $this->accountStateMachine->apply($account->getUuid(), action: AccountAction::REGISTER);

        return new SignupNewAccountResponse();
    }
}
