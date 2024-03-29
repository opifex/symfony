<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Notification\AccountActivatedNotification;
use App\Application\Notification\AccountRegisteredNotification;
use App\Domain\Event\AccountActivatedEvent;
use App\Domain\Event\AccountRegisteredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountEventListener
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
    ) {
    }

    #[AsEventListener(event: AccountActivatedEvent::class)]
    public function onAccountActivated(AccountActivatedEvent $event): void
    {
        $recipient = new Recipient($event->account->getEmail());
        $notification = new AccountActivatedNotification($event->account, $this->translator);
        $this->notifier->send($notification, $recipient);
    }

    #[AsEventListener(event: AccountRegisteredEvent::class)]
    public function onAccountRegistered(AccountRegisteredEvent $event): void
    {
        $recipient = new Recipient($event->account->getEmail());
        $notification = new AccountRegisteredNotification($event->account, $this->translator);
        $this->notifier->send($notification, $recipient);
    }
}
