<?php

declare(strict_types=1);

namespace App\Application\Command\CreateNewAccount;

use App\Application\Contract\UuidIdentityGeneratorInterface;
use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Localization\LocaleCode;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateNewAccountCommandHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private AccountPasswordHasherInterface $accountPasswordHasher,
        private UuidIdentityGeneratorInterface $uuidIdentityGenerator,
    ) {
    }

    public function __invoke(CreateNewAccountCommand $command): CreateNewAccountCommandResult
    {
        $account = Account::create(
            id: AccountIdentifier::fromString($this->uuidIdentityGenerator->generate()),
            email: EmailAddress::fromString($command->email),
            password: $this->accountPasswordHasher->hash($command->password),
            locale: LocaleCode::fromString($command->locale),
        )->register()->activate();

        $this->accountRepository->ensureEmailIsAvailable($account->email);
        $account = $this->accountRepository->save($account);

        return CreateNewAccountCommandResult::success($account);
    }
}
