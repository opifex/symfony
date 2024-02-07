<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Notification\AccountActivatedNotification;
use App\Application\Notification\AccountRegisteredNotification;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountAction;
use App\Domain\Event\AccountRegisteredEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountEventListener
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
    ) {
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::REGISTER)]
    public function onWorkflowAccountCompletedRegister(CompletedEvent $event): void
    {
        /** @var Account $account */
        $account = $event->getSubject();
        $recipient = new Recipient($account->getEmail());
        $notification = new AccountRegisteredNotification($account, $this->translator);

        $this->notifier->send($notification, $recipient);

        $account = $this->accountRepository->findOneByUuid($account->getUuid());
        $this->eventDispatcher->dispatch(new AccountRegisteredEvent($account));
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::ACTIVATE)]
    public function onWorkflowAccountCompletedActivate(CompletedEvent $event): void
    {
        /** @var Account $account */
        $account = $event->getSubject();
        $recipient = new Recipient($account->getEmail());
        $notification = new AccountActivatedNotification($account, $this->translator);

        $this->notifier->send($notification, $recipient);
    }
}
