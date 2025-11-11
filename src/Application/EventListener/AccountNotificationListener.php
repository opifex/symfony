<?php

declare(strict_types=1);

namespace App\Application\EventListener;

use App\Application\Notification\AccountRegisteredNotification;
use App\Domain\Account\Event\AccountRegisteredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountNotificationListener
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[AsEventListener(event: AccountRegisteredEvent::class)]
    public function onAccountRegistered(AccountRegisteredEvent $event): void
    {
        $recipient = new Recipient($event->getAccount()->getEmail()->toString());
        $notification = new AccountRegisteredNotification($event->getAccount(), $this->translator);
        $this->notifier->send($notification, $recipient);
    }
}
