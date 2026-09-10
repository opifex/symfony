<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateAccountById;

use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Localization\LocaleCode;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateAccountByIdCommandHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private AccountPasswordHasherInterface $accountPasswordHasher,
    ) {
    }

    public function __invoke(UpdateAccountByIdCommand $command): UpdateAccountByIdCommandResult
    {
        $accountId = AccountIdentifier::fromString($command->id);

        $account = $this->accountRepository->findOneById($accountId);

        if ($command->email !== null) {
            $emailAddress = EmailAddress::fromString($command->email);

            if (!$account->email->equals($emailAddress)) {
                $this->accountRepository->ensureEmailIsAvailable($emailAddress);
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

        $this->accountRepository->save($account);

        return UpdateAccountByIdCommandResult::success();
    }
}
