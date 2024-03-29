<?php

declare(strict_types=1);

namespace App\Application\Handler\SignupNewAccount;

use App\Application\Builder\AccountBuilder;
use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
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
        $hashedPassword = $this->accountPasswordHasher->hash($message->password);

        $accountBuilder = new AccountBuilder();
        $accountBuilder->setEmailAddress($message->email);
        $accountBuilder->setHashedPassword($hashedPassword);
        $accountBuilder->setDefaultLocale($message->locale);
        $accountBuilder->setAccessRoles([AccountRole::ROLE_USER]);
        $account = $accountBuilder->getAccount();

        $this->accountRepository->addOneAccount($account);
        $this->accountStateMachine->apply($account->getUuid(), action: AccountAction::REGISTER);

        return new SignupNewAccountResponse();
    }
}
