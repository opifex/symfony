<?php

declare(strict_types=1);

namespace App\Application\Command\SignupNewAccount;

use App\Application\Contract\EventMessageBusInterface;
use App\Application\Contract\UuidIdentityGeneratorInterface;
use App\Domain\Account\Account;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Event\AccountRegisteredEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SignupNewAccountCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly EventMessageBusInterface $eventMessageBus,
        private readonly UuidIdentityGeneratorInterface $uuidIdentityGenerator,
    ) {
    }

    public function __invoke(SignupNewAccountCommand $command): SignupNewAccountCommandResult
    {
        $this->accountEntityRepository->ensureEmailIsAvailable($command->email);
        $accountIdentifier = $this->uuidIdentityGenerator->generate();
        $accountPassword = $this->accountPasswordHasher->hash($command->password);
        $account = Account::create($accountIdentifier, $command->email, $accountPassword, $command->locale)
                |> $this->accountStateMachine->register(...)
                |> $this->accountStateMachine->activate(...)
                |> $this->accountEntityRepository->save(...);

        $accountRegisteredEvent = AccountRegisteredEvent::create($account);
        $this->eventMessageBus->publish($accountRegisteredEvent);

        return SignupNewAccountCommandResult::success();
    }
}
