<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Application\Factory\AccountFactory;
use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Contract\AccountStateMachineInterface;
use App\Domain\Entity\AccountAction;
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

    public function __invoke(CreateNewAccountCommand $message): CreateNewAccountResponse
    {
        $hashedPassword = $this->accountPasswordHasher->hash($message->password);

        $account = AccountFactory::createCustomAccount(
            emailAddress: $message->email,
            hashedPassword: $hashedPassword,
            defaultLocale: $message->locale,
            accessRoles: $message->roles,
        );

        $this->accountRepository->addOneAccount($account);
        $this->accountStateMachine->apply($account->getUuid(), action: AccountAction::REGISTER);

        $account = $this->accountRepository->findOneByUuid(uuid: $account->getUuid());

        return new CreateNewAccountResponse($account);
    }
}
