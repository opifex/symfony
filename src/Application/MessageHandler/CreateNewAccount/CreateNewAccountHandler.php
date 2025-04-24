<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Contract\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Entity\AccountRole;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AuthorizationForbiddenException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly AuthenticationPasswordHasherInterface $authenticationPasswordHasher,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(CreateNewAccountRequest $message): CreateNewAccountResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        if ($this->accountRepository->checkExistsByEmail($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $passwordHash = $this->authenticationPasswordHasher->hash($message->password);
        $accountUuid = $this->accountRepository->addOneAccount($message->email, $passwordHash, $message->locale);

        $this->accountStateMachine->register($accountUuid);
        $this->accountStateMachine->activate($accountUuid);

        return CreateNewAccountResult::success($accountUuid);
    }
}
