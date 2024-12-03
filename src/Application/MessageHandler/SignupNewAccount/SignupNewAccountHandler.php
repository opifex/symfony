<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

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
        $hashedPassword = $this->accountPasswordHasher->hash($message->password);

        $accountUuid = $this->accountRepository->addOneAccount($message->email, $hashedPassword);
        $this->accountRepository->updateLocaleByUuid($accountUuid, $message->locale);
        $this->accountRepository->updateRolesByUuid($accountUuid, role: AccountRole::User);
        $this->accountStateMachine->apply($accountUuid, action: AccountAction::Register);

        return new SignupNewAccountResponse();
    }
}
