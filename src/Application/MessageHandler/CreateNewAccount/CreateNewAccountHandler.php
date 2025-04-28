<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Contract\AccountEntityRepositoryInterface;
use App\Domain\Contract\AccountWorkflowManagerInterface;
use App\Domain\Contract\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AuthorizationForbiddenException;
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

        if ($this->accountEntityRepository->checkExistsByEmail($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $accountEntity = $this->accountEntityRepository->createEntityBuilder()
            ->withEmail($message->email)
            ->withPassword($this->authenticationPasswordHasher->hash($message->password))
            ->withLocale($message->locale)
            ->build();

        $accountUuid = $this->accountEntityRepository->addOneAccount($accountEntity);

        $this->accountWorkflowManager->register($accountUuid);
        $this->accountWorkflowManager->activate($accountUuid);

        return CreateNewAccountResult::success($accountUuid);
    }
}
