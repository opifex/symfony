<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Application\Contract\ApplicationEventDispatcherInterface;
use App\Application\Contract\UserPasswordHasherInterface;
use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\Account;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\AccountRole;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly ApplicationEventDispatcherInterface $applicationEventDispatcher,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(CreateNewAccountRequest $request): CreateNewAccountResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::Admin);

        if ($this->accountEntityRepository->findOneByEmail($request->email) !== null) {
            throw AccountAlreadyExistsException::create();
        }

        $hashedPassword = $this->userPasswordHasher->hash($request->password);
        $account = Account::create($request->email, $hashedPassword, $request->locale);
        $account = $this->accountStateMachine->register($account);
        $account = $this->accountStateMachine->activate($account);
        $this->accountEntityRepository->save($account);

        $accountRegisteredEvent = AccountRegisteredEvent::create($account);
        $this->applicationEventDispatcher->dispatch($accountRegisteredEvent);

        return CreateNewAccountResult::success($account);
    }
}
