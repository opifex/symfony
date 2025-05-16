<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Domain\Contract\Account\AccountEntityBuilderInterface;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Contract\Authentication\AuthenticationPasswordHasherInterface;
use App\Domain\Exception\Account\AccountAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private readonly AccountEntityBuilderInterface $accountEntityBuilder,
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountWorkflowManagerInterface $accountWorkflowManager,
        private readonly AuthenticationPasswordHasherInterface $authenticationPasswordHasher,
    ) {
    }

    public function __invoke(SignupNewAccountRequest $message): SignupNewAccountResult
    {
        if ($this->accountEntityRepository->checkEmailExists($message->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $passwordHash = $this->authenticationPasswordHasher->hash($message->password);
        $accountEntity = $this->accountEntityBuilder->build($message->email, $passwordHash, $message->locale);
        $accountUuid = $this->accountEntityRepository->save($accountEntity);

        $this->accountWorkflowManager->register($accountUuid);
        $this->accountWorkflowManager->activate($accountUuid);

        return SignupNewAccountResult::success();
    }
}
