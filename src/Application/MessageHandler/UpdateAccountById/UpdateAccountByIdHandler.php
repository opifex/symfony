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

    public function __invoke(UpdateAccountByIdRequest $message): UpdateAccountByIdResult
    {
        if (!$this->authorizationTokenManager->checkPermission(access: AccountRole::ADMIN)) {
            throw AuthorizationForbiddenException::create();
        }

        $account = $this->accountEntityRepository->findOneByUuid($message->uuid);

        if (!$account instanceof Account) {
            throw AccountNotFoundException::create();
        }

        if ($message->email !== null) {
            if ($message->email !== $account->email) {
                if ($this->accountEntityRepository->findOneByEmail($message->email)) {
                    throw AccountAlreadyExistsException::create();
                }

                $account->email = $message->email;
            }
        }

        if ($message->password !== null) {
            $account->password = $this->authenticationPasswordHasher->hash($message->password);
        }

        if ($message->locale !== null) {
            $account->locale = $message->locale;
        }

        return UpdateAccountByIdResult::success();
    }
}
