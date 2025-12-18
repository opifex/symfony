<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Application\Contract\UserPasswordHasherInterface;
use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Account\AccountRole;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\HashedPassword;
use App\Domain\Localization\LocaleCode;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateAccountByIdHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AuthorizationTokenManagerInterface $authorizationTokenManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(UpdateAccountByIdRequest $request): UpdateAccountByIdResult
    {
        $this->authorizationTokenManager->checkUserPermission(role: AccountRole::Admin);

        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        if ($request->email !== null) {
            if (!$account->getEmail()->equals(EmailAddress::fromString($request->email))) {
                if ($this->accountEntityRepository->findOneByEmail($request->email) !== null) {
                    throw AccountAlreadyExistsException::create();
                }

                $account = $account->withEmail(EmailAddress::fromString($request->email));
            }
        }

        if ($request->password !== null) {
            $passwordHash = $this->userPasswordHasher->hash($request->password);
            $account = $account->withPassword(HashedPassword::fromString($passwordHash));
        }

        if ($request->locale !== null) {
            $account = $account->withLocale(LocaleCode::fromString($request->locale));
        }

        $this->accountEntityRepository->save($account);

        return UpdateAccountByIdResult::success();
    }
}
