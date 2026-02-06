<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Command\CreateNewAccount;

use App\Application\Contract\EventMessageBusInterface;
use App\Application\Contract\UuidIdentityGeneratorInterface;
use App\Domain\Account\Account;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Contract\AccountPasswordHasherInterface;
use App\Domain\Account\Contract\AccountStateMachineInterface;
use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateNewAccountCommandHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly AccountPasswordHasherInterface $accountPasswordHasher,
        private readonly AccountStateMachineInterface $accountStateMachine,
        private readonly EventMessageBusInterface $eventMessageBus,
        private readonly UuidIdentityGeneratorInterface $uuidIdentityGenerator,
    ) {
    }

    public function __invoke(CreateNewAccountCommand $command): CreateNewAccountCommandResult
    {
        if ($this->accountEntityRepository->findOneByEmail($command->email) !== null) {
            throw AccountAlreadyExistsException::create();
        }

        $accountIdentifier = $this->uuidIdentityGenerator->generate();
        $accountPassword = $this->accountPasswordHasher->hash($command->password);
        $account = Account::create($accountIdentifier, $command->email, $accountPassword, $command->locale)
                |> $this->accountStateMachine->register(...)
                |> $this->accountStateMachine->activate(...)
                |> $this->accountEntityRepository->save(...);

        $accountRegisteredEvent = AccountRegisteredEvent::create($account);
        $this->eventMessageBus->publish($accountRegisteredEvent);

        return CreateNewAccountCommandResult::success($account);
    }
}
