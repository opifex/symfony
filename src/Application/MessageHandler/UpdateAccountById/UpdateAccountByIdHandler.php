<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\UpdateAccountById;

use App\Domain\Contract\AccountEntityRepositoryInterface;
use App\Domain\Contract\AuthenticationPasswordHasherInterface;
use App\Domain\Contract\AuthorizationTokenManagerInterface;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AuthorizationForbiddenException;
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

        if ($message->email !== null) {
            if ($message->email !== $account->getEmail()) {
                if ($this->accountEntityRepository->checkEmailExists($message->email)) {
                    throw AccountAlreadyExistsException::create();
                }

                $this->accountEntityRepository->updateEmailByUuid($message->uuid, $message->email);
            }
        }

        if ($message->password !== null) {
            $passwordHash = $this->authenticationPasswordHasher->hash($message->password);
            $this->accountEntityRepository->updatePasswordByUuid($message->uuid, $passwordHash);
        }

        if ($message->locale !== null) {
            $this->accountEntityRepository->updateLocaleByUuid($message->uuid, $message->locale);
        }

        return UpdateAccountByIdResult::success();
    }
}
