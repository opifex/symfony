<?php

declare(strict_types=1);

namespace App\Application\EventListener;

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
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::Register->value)]
    public function onWorkflowAccountCompletedRegister(CompletedEvent $event): void
    {
        /** @var Account $subject */
        $subject = $event->getSubject();
        $account = $this->accountRepository->findOneByUuid($subject->getUuid());
        $this->eventDispatcher->dispatch(new AccountRegisteredEvent($account));
    }
}
