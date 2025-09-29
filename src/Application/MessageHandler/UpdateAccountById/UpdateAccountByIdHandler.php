<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Application\Contract\AuthenticationPasswordHasherInterface;
use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Common\LocaleCode;
use App\Domain\Common\Role;
use App\Domain\Common\ValueObject\EmailAddress;
use App\Domain\Common\ValueObject\HashedPassword;
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
                if ($this->accountEntityRepository->findOneByEmail($request->email) !== null) {
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
