<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(CreateNewAccountRequest $message): CreateNewAccountResponse
    {
        try {
            $this->accountRepository->findOneByEmail($message->email);
            throw AccountAlreadyExistsException::create();
        } catch (AccountNotFoundException) {
            $transformRoleClosure = static fn(string $role) => AccountRole::fromValue($role);
            $accessRoles = array_map($transformRoleClosure, $message->roles);
            $hashedPassword = $this->accountPasswordHasher->hash($message->password);

            $accountUuid = $this->accountRepository->addOneAccount($message->email, $hashedPassword);
            $this->accountRepository->updateLocaleByUuid($accountUuid, $message->locale);
            $this->accountRepository->updateRolesByUuid($accountUuid, ...$accessRoles);
            $this->accountStateMachine->apply($accountUuid, action: AccountAction::Register);

            $account = $this->accountRepository->findOneByUuid($accountUuid);

            return new CreateNewAccountResponse($account);
        }
    }
}
