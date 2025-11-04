<?php

declare(strict_types=1);

namespace App\Infrastructure\Workflow;

use App\Domain\Account\Account;
use App\Domain\Account\AccountAction;
use App\Domain\Account\Event\AccountRegisteredEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;

final class AccountWorkflowListener
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::Register->value)]
    public function onWorkflowAccountCompletedRegister(CompletedEvent $event): void
    {
        $account = $event->getSubject();

        if (!$account instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        $this->eventDispatcher->dispatch(new AccountRegisteredEvent($account));
    }
}
