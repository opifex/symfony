<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Contract\AuthenticationPasswordHasherInterface;
use App\Domain\Exception\AccountAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly AuthenticationPasswordHasherInterface $authenticationPasswordHasher,
    ) {
    }

    public function __invoke(SignupNewAccountRequest $message): SignupNewAccountResult
    {
        if ($this->accountRepository->checkExistsByEmail($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $passwordHash = $this->authenticationPasswordHasher->hash($message->password);
        $accountUuid = $this->accountRepository->addOneAccount($message->email, $passwordHash, $message->locale);

        $this->accountStateMachine->register($accountUuid);
        $this->accountStateMachine->activate($accountUuid);

        return SignupNewAccountResult::success();
    }
}
