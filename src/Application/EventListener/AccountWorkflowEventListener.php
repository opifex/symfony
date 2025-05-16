<?php

declare(strict_types=1);

namespace App\Application\EventListener;

use App\Application\Event\AccountRegisteredEvent;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Model\Account;
use App\Domain\Model\AccountAction;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;

final class AccountWorkflowEventListener
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::REGISTER)]
    public function onWorkflowAccountCompletedRegister(CompletedEvent $event): void
    {
        if (!$event->getSubject() instanceof Account) {
            throw new InvalidArgumentException(message: 'Subject expected to be a valid account.');
        }

        $account = $this->accountEntityRepository->findOneByUuid($event->getSubject()->getUuid());
        $this->eventDispatcher->dispatch(new AccountRegisteredEvent($account));
    }
}
