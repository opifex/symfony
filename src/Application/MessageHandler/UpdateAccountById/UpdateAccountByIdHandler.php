<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authentication\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountAlreadyExistsException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Common\EmailAddress;
use App\Domain\Model\Common\HashedPassword;
use App\Domain\Model\LocaleCode;
use App\Domain\Model\Role;
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
        $this->authorizationTokenManager->checkUserPermission(role: Role::Admin);

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        if ($request->email !== null) {
            if (!$account->getEmail()->equals(EmailAddress::fromString($request->email))) {
                if ($this->accountEntityRepository->findOneByEmail($request->email)) {
                    throw AccountAlreadyExistsException::create();
                }

                $account->changeEmail(EmailAddress::fromString($request->email));
            }
        }

        if ($request->password !== null) {
            $passwordHash = $this->authenticationPasswordHasher->hash($request->password);
            $account->changePassword(HashedPassword::fromString($passwordHash));
        }

        if ($request->locale !== null) {
            $account->switchLocale(LocaleCode::fromString($request->locale));
        }

        $this->accountEntityRepository->save($account);

        return UpdateAccountByIdResult::success();
    }
}
