<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Application\Service\AccountEntityBuilder;
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

    public function __invoke(CreateNewAccountRequest $message): CreateNewAccountResponse
    {
        $accountBuilder = new AccountEntityBuilder($this->accountPasswordHasher);
        $accountBuilder->setEmailAddress($message->email);
        $accountBuilder->setPlainPassword($message->password);
        $accountBuilder->setLocaleCode($message->locale);
        $accountBuilder->setAccessRoles($message->roles);
        $account = $accountBuilder->getAccount();

        $this->accountRepository->addOneAccount($account);
        $this->accountStateMachine->apply($account->getUuid(), action: AccountAction::Register);

        $account = $this->accountRepository->findOneByUuid(uuid: $account->getUuid());

        return new CreateNewAccountResponse($account);
    }
}
