<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use App\Domain\Exception\AccountAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(SignupNewAccountRequest $message): SignupNewAccountResponse
    {
        if ($this->accountRepository->isExistsByEmail($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $hashedPassword = $this->accountPasswordHasher->hash($message->password);

        $accountUuid = $this->accountRepository->addOneAccount($message->email, $hashedPassword);
        $this->accountRepository->updateLocaleByUuid($accountUuid, $message->locale);
        $this->accountStateMachine->apply($accountUuid, action: AccountAction::Register);

        return SignupNewAccountResponse::create();
    }
}
