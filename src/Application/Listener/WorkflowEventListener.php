<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountAction;
use App\Domain\Event\AccountActivatedEvent;
use App\Domain\Event\AccountRegisteredEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Event\CompletedEvent;

final class WorkflowEventListener
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::REGISTER)]
    public function onWorkflowAccountCompletedRegister(CompletedEvent $event): void
    {
        /** @var Account $subject */
        $subject = $event->getSubject();
        $account = $this->accountRepository->findOneByUuid($subject->getUuid());
        $this->eventDispatcher->dispatch(new AccountRegisteredEvent($account));
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::ACTIVATE)]
    public function onWorkflowAccountCompletedActivate(CompletedEvent $event): void
    {
        /** @var Account $subject */
        $subject = $event->getSubject();
        $account = $this->accountRepository->findOneByUuid($subject->getUuid());
        $this->eventDispatcher->dispatch(new AccountActivatedEvent($account));
    }
}
