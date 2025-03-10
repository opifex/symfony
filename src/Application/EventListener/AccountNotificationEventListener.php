<?php

declare(strict_types=1);

namespace App\Application\EventListener;

use App\Application\Event\AccountActivatedEvent;
use App\Application\Event\AccountRegisteredEvent;
use App\Application\Notification\AccountActivatedNotification;
use App\Application\Notification\AccountRegisteredNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountNotificationEventListener
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[AsEventListener(event: AccountActivatedEvent::class)]
    public function onAccountActivated(AccountActivatedEvent $event): void
    {
        $recipient = new Recipient($event->getAccount()->getEmail());
        $notification = new AccountActivatedNotification($event->getAccount(), $this->translator);
        $this->notifier->send($notification, $recipient);
    }

    #[AsEventListener(event: AccountRegisteredEvent::class)]
    public function onAccountRegistered(AccountRegisteredEvent $event): void
    {
        $recipient = new Recipient($event->getAccount()->getEmail());
        $notification = new AccountRegisteredNotification($event->getAccount(), $this->translator);
        $this->notifier->send($notification, $recipient);
    }
}
