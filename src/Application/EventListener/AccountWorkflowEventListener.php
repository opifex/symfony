<?php

declare(strict_types=1);

namespace App\Application\EventListener;

use App\Application\Event\AccountActivatedEvent;
use App\Application\Event\AccountRegisteredEvent;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountAction;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Event\CompletedEvent;

final class AccountWorkflowEventListener
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[AsCompletedListener(workflow: 'account')]
    public function onWorkflowAccountCompleted(CompletedEvent $event): void
    {
        /** @var Account $subject */
        $subject = $event->getSubject();
        $status = (string) key($event->getMarking()->getPlaces());
        $this->accountRepository->updateStatusByUuid($subject->getUuid(), $status);
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
