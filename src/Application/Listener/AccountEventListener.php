<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Notification\AccountCreateNotification;
use App\Domain\Entity\AccountDetails;
use App\Domain\Event\AccountCreateEvent;
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
        $details = new AccountDetails($event->account->getEmail());
        $notification = new AccountCreateNotification($details, channels: ['email']);

        $this->notifier->send($notification, $recipient);
    }
}
