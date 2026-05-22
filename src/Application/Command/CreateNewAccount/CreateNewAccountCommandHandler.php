<?php

declare(strict_types=1);

namespace App\Application\Command\CreateNewAccount;

use App\Application\Contract\EventMessageBusInterface;
use App\Application\Contract\UuidIdentityGeneratorInterface;
use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Localization\LocaleCode;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateNewAccountCommandHandler
{
    public function __construct(
        private AccountEntityRepositoryInterface $accountEntityRepository,
        private AccountPasswordHasherInterface $accountPasswordHasher,
        private EventMessageBusInterface $eventMessageBus,
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

        $this->accountEntityRepository->ensureEmailIsAvailable($account->email);
        $account = $this->accountEntityRepository->save($account);

        $accountRegisteredEvent = AccountRegisteredEvent::create($account);
        $this->eventMessageBus->publish($accountRegisteredEvent);

        return CreateNewAccountCommandResult::success($account);
    }
}
