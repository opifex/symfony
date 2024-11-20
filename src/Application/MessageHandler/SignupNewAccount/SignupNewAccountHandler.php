<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Application\Service\AccountEntityBuilder;
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

    public function __invoke(SignupNewAccountRequest $message): SignupNewAccountResponse
    {
        $accountBuilder = new AccountEntityBuilder($this->accountPasswordHasher);
        $accountBuilder->setEmailAddress($message->email);
        $accountBuilder->setPlainPassword($message->password);
        $accountBuilder->setDefaultLocale($message->locale);
        $accountBuilder->setAccessRoles([AccountRole::ROLE_USER]);
        $account = $accountBuilder->getAccount();

        $this->accountRepository->addOneAccount($account);
        $this->accountStateMachine->apply($account->getUuid(), action: AccountAction::Register);

        return new SignupNewAccountResponse();
    }
}
