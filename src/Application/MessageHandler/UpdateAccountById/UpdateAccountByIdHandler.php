<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\AccountRole;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateAccountByIdHandler
{
    public function __construct(
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(UpdateAccountByIdRequest $message): UpdateAccountByIdResponse
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
            $passwordHash = $this->accountPasswordHasher->hash($message->password);
            $this->accountRepository->updatePasswordByUuid($message->uuid, $passwordHash);
        }

        if ($message->locale !== null) {
            $this->accountRepository->updateLocaleByUuid($message->uuid, $message->locale);
        }

        if ($message->roles !== null) {
            $transformRoleClosure = static fn(string $role) => AccountRole::fromValue($role);
            $accountRoles = array_map($transformRoleClosure, $message->roles);
            $this->accountRepository->updateRolesByUuid($message->uuid, ...$accountRoles);
        }

        return new UpdateAccountByIdResponse();
    }
}
