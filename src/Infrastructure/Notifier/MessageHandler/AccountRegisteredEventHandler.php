<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier\MessageHandler;

use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Infrastructure\Notifier\TemplatedEmailNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
final readonly class AccountRegisteredEventHandler
{
    private const string SUBJECT = 'Thank you for registration';

    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(AccountRegisteredEvent $event): void
    {
        $locale = $event->accountLocale;
        $subject = $this->translator->trans(self::SUBJECT, locale: $locale);
        $context = ['account' => ['email' => $event->accountEmail]];

        $templatedEmail = new TemplatedEmail();
        $templatedEmail->subject($subject);
        $templatedEmail->locale($locale);
        $templatedEmail->htmlTemplate(template: '@emails/account.registered.html.twig');
        $templatedEmail->context([...['locale' => $locale], ...$context]);

        $recipient = new Recipient($event->accountEmail);
        $notification = new TemplatedEmailNotification($templatedEmail);

        $this->notifier->send($notification, $recipient);
    }
}
