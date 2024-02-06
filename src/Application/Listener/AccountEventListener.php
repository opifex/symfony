<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Notification\AccountCreatedNotification;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountAction;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountEventListener
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
    ) {
    }

    #[AsCompletedListener(workflow: 'account', transition: AccountAction::ACTIVATE)]
    public function onWorkflowAccountCompletedActivate(CompletedEvent $event): void
    {
        $account = $event->getSubject();

        if (!$account instanceof Account) {
            return;
        }

        $recipient = new Recipient($account->getEmail());
        $notification = new AccountCreatedNotification($account, $this->translator);

        $this->notifier->send($notification, $recipient);
    }
}
