<?php

declare(strict_types=1);

namespace App\Application\Handler\UpdateAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsMessageHandler]
final class UpdateAccountByIdHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    public function __invoke(UpdateAccountByIdCommand $message): UpdateAccountByIdResponse
    {
        $account = $this->accountRepository->findOneByUuid($message->uuid);

        if ($message->email !== null) {
            if ($message->email !== $account->getEmail()) {
                try {
                    $this->accountRepository->findOneByEmail($message->email);
                    throw new AccountAlreadyExistsException(
                        message: 'Email address is already associated with another account.',
                    );
                } catch (AccountNotFoundException) {
                    $this->accountRepository->updateEmailByUuid($message->uuid, $message->email);
                }
            }
        }

        if ($message->password !== null) {
            $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(user: Account::class);
            $passwordHash = $passwordHasher->hash($message->password);

            $this->accountRepository->updatePasswordByUuid($message->uuid, $passwordHash);
        }

        if ($message->locale !== null) {
            $this->accountRepository->updateLocaleByUuid($message->uuid, $message->locale);
        }

        if ($message->roles !== null) {
            $this->accountRepository->updateRolesByUuid($message->uuid, $message->roles);
        }

        return new UpdateAccountByIdResponse();
    }
}
