<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Application\Contract\AuthenticationPasswordHasherInterface;
use App\Domain\Account\Account;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountWorkflowManagerInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
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
        if ($this->accountEntityRepository->findOneByEmail($request->email) !== null) {
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
