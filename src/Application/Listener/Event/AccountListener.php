<?php

declare(strict_types=1);

namespace App\Application\Listener\Event;

use App\Domain\Event\AccountCreateEvent;
use App\Domain\Notification\AbstractNotification;
use App\Domain\Notification\Account\AccountCreateNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountListener
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
    ) {
    }

    #[AsEventListener(event: AccountCreateEvent::class)]
    public function onAccountCreate(AccountCreateEvent $event): void
    {
        $recipient = new Recipient($event->account->getEmail());
        $notification = new AccountCreateNotification(
            channels: [AbstractNotification::CHANNEL_EMAIL],
            translator: $this->translator,
        );
        $notification->accountEmail = $event->account->getEmail();

        $this->notifier->send($notification, $recipient);
    }
}
