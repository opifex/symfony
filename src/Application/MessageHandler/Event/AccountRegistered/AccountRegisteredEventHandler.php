<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Event\AccountRegistered;

use App\Application\Notification\AccountRegisteredNotification;
use App\Domain\Account\Event\AccountRegisteredEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
final class AccountRegisteredEventHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(AccountRegisteredEvent $event): void
    {
        $recipient = new Recipient($event->getAccount()->getEmail()->toString());
        $notification = new AccountRegisteredNotification($event->getAccount(), $this->translator);
        $this->notifier->send($notification, $recipient);
    }
}
