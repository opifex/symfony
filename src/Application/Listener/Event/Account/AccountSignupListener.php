<?php

declare(strict_types=1);

namespace App\Application\Listener\Event\Account;

use App\Domain\Event\Account\AccountCreatedEvent;
use App\Domain\Notification\AbstractNotification;
use App\Domain\Notification\Account\AccountSignupNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: AccountCreatedEvent::class)]
class AccountSignupListener
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(AccountCreatedEvent $event): void
    {
        $recipient = new Recipient($event->account->getEmail());
        $notification = new AccountSignupNotification(
            channels: [AbstractNotification::CHANNEL_EMAIL],
            translator: $this->translator,
        );
        $notification->accountEmail = $event->account->getEmail();

        $this->notifier->send($notification, $recipient);
    }
}
