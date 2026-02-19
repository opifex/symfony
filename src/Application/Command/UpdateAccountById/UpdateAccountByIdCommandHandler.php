<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Foundation\ValueObject\EmailAddress;
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
        $accountId = AccountIdentifier::fromString($command->id);

        $account = $this->accountEntityRepository->findOneById($accountId);

        if ($command->email !== null) {
            $emailAddress = EmailAddress::fromString($command->email);

            if (!$account->getEmail()->equals($emailAddress)) {
                $this->accountEntityRepository->ensureEmailIsAvailable($emailAddress);
                $account = $account->withEmail($emailAddress);
            }
        }

        if ($command->password !== null) {
            $accountPassword = $this->accountPasswordHasher->hash($command->password);
            $account = $account->withPassword($accountPassword);
        }

        if ($command->locale !== null) {
            $accountLocale = LocaleCode::fromString($command->locale);
            $account = $account->withLocale($accountLocale);
        }

        $this->accountEntityRepository->save($account);

        return UpdateAccountByIdCommandResult::success();
    }
}
