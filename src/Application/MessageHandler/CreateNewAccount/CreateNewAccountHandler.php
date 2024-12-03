<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
use App\Domain\Entity\AccountRole;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private AccountPasswordHasherInterface $accountPasswordHasher,
        private AccountRepositoryInterface $accountRepository,
        private AccountStateMachineInterface $accountStateMachine,
    ) {
    }

    public function __invoke(CreateNewAccountRequest $message): CreateNewAccountResponse
    {
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
