<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\UpdateAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateAccountByIdCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
    ) {
    }

    public function __invoke(UpdateAccountByIdCommand $command): UpdateAccountByIdCommandResult
    {
        $account = $this->accountEntityRepository->findOneById($command->id)
            ?? throw AccountNotFoundException::create();

        if ($command->email !== null) {
            if (!$account->getEmail()->equals($command->email)) {
                if ($this->accountEntityRepository->findOneByEmail($command->email) !== null) {
                    throw AccountAlreadyExistsException::create();
                }

                $account = $account->withEmail($command->email);
            }
        }

        if ($command->password !== null) {
            $accountPassword = $this->accountPasswordHasher->hash($command->password);
            $account = $account->withPassword($accountPassword);
        }

        if ($command->locale !== null) {
            $account = $account->withLocale($command->locale);
        }

        $this->accountEntityRepository->save($account);

        return UpdateAccountByIdCommandResult::success();
    }
}
