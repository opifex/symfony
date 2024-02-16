<?php

declare(strict_types=1);

namespace App\Application\Handler\UpdateAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final class UpdateAccountByIdHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(UpdateAccountByIdCommand $message): UpdateAccountByIdResponse
    {
        $account = $this->accountRepository->findOneByUuid($message->uuid);

        if ($message->email !== null && $message->email !== $account->getEmail()) {
            try {
                $this->accountRepository->findOneByEmail($message->email);
                throw new AccountAlreadyExistsException(
                    message: 'Email address is already associated with another account.',
                );
            } catch (AccountNotFoundException) {
                $this->accountRepository->updateEmailByUuid($account->getUuid(), $message->email);
            }
        }

        if ($message->password !== null) {
            $password = $this->userPasswordHasher->hashPassword($account, $message->password);
            $this->accountRepository->updatePasswordByUuid($account->getUuid(), $password);
        }

        if ($message->locale !== null) {
            $this->accountRepository->updateLocaleByUuid($account->getUuid(), $message->locale);
        }

        if ($message->roles !== null) {
            $this->accountRepository->updateRolesByUuid($account->getUuid(), $message->roles);
        }

        return new UpdateAccountByIdResponse();
    }
}
