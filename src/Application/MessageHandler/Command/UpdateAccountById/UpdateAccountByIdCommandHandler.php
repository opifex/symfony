<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\UpdateAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\HashedPassword;
use App\Domain\Localization\LocaleCode;
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
            if (!$account->getEmail()->equals(EmailAddress::fromString($command->email))) {
                if ($this->accountEntityRepository->findOneByEmail($command->email) !== null) {
                    throw AccountAlreadyExistsException::create();
                }

                $account = $account->withEmail(EmailAddress::fromString($command->email));
            }
        }

        if ($command->password !== null) {
            $passwordHash = $this->accountPasswordHasher->hash($command->password);
            $account = $account->withPassword(HashedPassword::fromString($passwordHash));
        }

        if ($command->locale !== null) {
            $account = $account->withLocale(LocaleCode::fromString($command->locale));
        }

        $this->accountEntityRepository->save($account);

        return UpdateAccountByIdCommandResult::success();
    }
}
