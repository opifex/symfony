<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authentication\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountAlreadyExistsException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
use App\Domain\Model\AccountRole;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateAccountByIdHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthenticationPasswordHasherInterface $authenticationPasswordHasher,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
    ) {
    }

    public function __invoke(UpdateAccountByIdRequest $request): UpdateAccountByIdResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        $accountIdentifier = new AccountIdentifier($request->id);
        $account = $this->accountEntityRepository->findOneByid($accountIdentifier);

        if (!$account instanceof Account) {
            throw AccountNotFoundException::create();
        }

        if ($request->email !== null) {
            if ($request->email !== $account->getEmail()) {
                if ($this->accountEntityRepository->findOneByEmail($request->email)) {
                    throw AccountAlreadyExistsException::create();
                }

                $account->changeEmail($request->email);
            }
        }

        if ($request->password !== null) {
            $passwordHash = $this->authenticationPasswordHasher->hash($request->password);
            $account->changePassword($passwordHash);
        }

        if ($request->locale !== null) {
            $account->switchLocale($request->locale);
        }

        $this->accountEntityRepository->save($account);

        return UpdateAccountByIdResult::success();
    }
}
