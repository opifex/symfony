<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Domain\Contract\AccountPasswordHasherInterface;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountAlreadyExistsException;
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
                if ($this->accountRepository->checkExistsByEmail($message->email)) {
                    throw AccountAlreadyExistsException::create();
                }

                $this->accountRepository->updateEmailByUuid($message->uuid, $message->email);
            }
        }

        if ($message->password !== null) {
            $passwordHash = $this->accountPasswordHasher->hash($message->password);
            $this->accountRepository->updatePasswordByUuid($message->uuid, $passwordHash);
        }

        if ($message->locale !== null) {
            $this->accountRepository->updateLocaleByUuid($message->uuid, $message->locale);
        }

        return UpdateAccountByIdResponse::create();
    }
}
