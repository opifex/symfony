<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Event\AccountCreateEvent;
use App\Domain\Notification\AccountCreateNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final class AccountEventListener
{
    public function __construct(private NotifierInterface $notifier)
    {
    }

    #[AsEventListener(event: AccountCreateEvent::class)]
    public function onAccountCreate(AccountCreateEvent $event): void
    {
        $recipient = new Recipient($event->account->getEmail());
        $notification = new AccountCreateNotification(channels: ['email']);

        $this->notifier->send($notification, $recipient);
    }
}
