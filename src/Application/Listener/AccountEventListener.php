<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Notification\AccountCreatedNotification;
use App\Domain\Event\AccountCreatedEvent;
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

    #[AsEventListener(event: AccountCreatedEvent::class)]
    public function onAccountCreated(AccountCreatedEvent $event): void
    {
        $recipient = new Recipient($event->account->getEmail());
        $notification = new AccountCreatedNotification($event->account, $this->translator);

        $this->notifier->send($notification, $recipient);
    }
}
