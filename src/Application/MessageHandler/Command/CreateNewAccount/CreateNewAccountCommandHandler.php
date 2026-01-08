<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\CreateNewAccount;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Application\Contract\EventMessageBusInterface;
use App\Domain\Account\Account;
use App\Domain\Account\AccountRole;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly EventMessageBusInterface $eventMessageBus,
    ) {
    }

    public function __invoke(CreateNewAccountCommand $request): CreateNewAccountCommandResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::Admin);

        if ($this->accountEntityRepository->findOneByEmail($request->email) !== null) {
            throw AccountAlreadyExistsException::create();
        }

        $hashedPassword = $this->accountPasswordHasher->hash($request->password);
        $account = Account::create($request->email, $hashedPassword, $request->locale);
        $account = $this->accountStateMachine->register($account);
        $account = $this->accountStateMachine->activate($account);
        $this->accountEntityRepository->save($account);

        $accountRegisteredEvent = AccountRegisteredEvent::create($account);
        $this->eventMessageBus->publish($accountRegisteredEvent);

        return CreateNewAccountCommandResult::success($account);
    }
}
