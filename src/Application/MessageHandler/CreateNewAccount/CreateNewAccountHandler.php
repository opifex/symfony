<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Entity\AccountRole;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AuthorizationForbiddenException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(CreateNewAccountRequest $message): CreateNewAccountResponse
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        if ($this->accountRepository->checkExistsByEmail($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $hashedPassword = $this->accountPasswordHasher->hash($message->password);

        $accountUuid = $this->accountRepository->addOneAccount($message->email, $hashedPassword);
        $this->accountRepository->updateLocaleByUuid($accountUuid, $message->locale);
        $this->accountStateMachine->register($accountUuid);
        $this->accountStateMachine->activate($accountUuid);

        return CreateNewAccountResponse::create($accountUuid);
    }
}
