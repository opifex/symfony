<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Contract\Authentication\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountAlreadyExistsException;
use App\Domain\Model\Account;
use App\Domain\Model\Role;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountWorkflowManagerInterface $accountWorkflowManager,
        private readonly AuthenticationPasswordHasherInterface $authenticationPasswordHasher,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(CreateNewAccountRequest $request): CreateNewAccountResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: Role::Admin);

        if ($this->accountEntityRepository->findOneByEmail($request->email) !== null) {
            throw AccountAlreadyExistsException::create();
        }

        $hashedPassword = $this->authenticationPasswordHasher->hash($request->password);
        $account = Account::create($request->email, $hashedPassword, $request->locale);

        $this->accountWorkflowManager->register($account);
        $this->accountWorkflowManager->activate($account);
        $this->accountEntityRepository->save($account);

        return CreateNewAccountResult::success($account);
    }
}
