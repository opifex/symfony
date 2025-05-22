<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Contract\Authentication\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountAlreadyExistsException;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountRole;
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

    public function __invoke(CreateNewAccountRequest $message): CreateNewAccountResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        if ($this->accountEntityRepository->findOneByEmail($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $passwordHash = $this->authenticationPasswordHasher->hash($message->password);

        $account = Account::create($message->email, $passwordHash, $message->locale);
        $this->accountEntityRepository->save($account);

        $this->accountWorkflowManager->register($account);
        $this->accountWorkflowManager->activate($account);

        return CreateNewAccountResult::success($account);
    }
}
