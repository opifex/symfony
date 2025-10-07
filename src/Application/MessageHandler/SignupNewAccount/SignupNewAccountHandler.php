<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SignupNewAccount;

use App\Application\Contract\AuthenticationPasswordHasherInterface;
use App\Domain\Account\Account;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountStateMachineInterface $accountStateMachine,
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

        $this->accountStateMachine->register($account);
        $this->accountStateMachine->activate($account);
        $this->accountEntityRepository->save($account);

        return SignupNewAccountResult::success();
    }
}
