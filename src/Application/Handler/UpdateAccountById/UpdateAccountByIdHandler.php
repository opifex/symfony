<?php

declare(strict_types=1);

namespace App\Application\Handler\UpdateAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateAccountByIdHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(UpdateAccountByIdCommand $message): void
    {
        $account = $this->accountRepository->findOneByUuid($message->uuid);

        if ($message->email !== null && $message->email !== $account->getEmail()) {
            try {
                $this->accountRepository->findOneByEmail($message->email);
                throw new ConflictHttpException(
                    message: 'Email address is already associated with another account.',
                );
            } catch (AccountNotFoundException) {
                $account->setEmail($message->email);
            }
        }

        if ($message->roles !== null) {
            $account->setRoles($message->roles);
        }

        if ($message->password !== null) {
            $account->setPassword($this->userPasswordHasher->hashPassword($account, $message->password));
        }
    }
}
