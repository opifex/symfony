<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Account\AccountWorkflowManagerInterface;
use App\Domain\Contract\Authentication\AuthenticationPasswordHasherInterface;
use App\Domain\Exception\Account\AccountAlreadyExistsException;
use App\Domain\Model\Account;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountWorkflowManagerInterface $accountWorkflowManager,
        private readonly AuthenticationPasswordHasherInterface $authenticationPasswordHasher,
    ) {
    }

    public function __invoke(SignupNewAccountRequest $request): SignupNewAccountResult
    {
        if ($this->accountEntityRepository->findOneByEmail($request->email)) {
            throw AccountAlreadyExistsException::create();
        }

        $hashedPassword = $this->authenticationPasswordHasher->hash($request->password);
        $account = Account::create($request->email, $hashedPassword, $request->locale);

        $this->accountWorkflowManager->register($account);
        $this->accountWorkflowManager->activate($account);
        $this->accountEntityRepository->save($account);

        return SignupNewAccountResult::success();
    }
}
