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
final class AccountRegisteredEventHandler
{
    private string $subject = 'Thank you for registration';

    private string $template = '@emails/account.registered.html.twig';

    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(AccountRegisteredEvent $event): void
    {
        $locale = $event->getAccount()->getLocale()->toString();
        $subject = $this->translator->trans($this->subject, locale: $locale);
        $context = ['account' => ['email' => $event->getAccount()->getEmail()->toString()]];

        $templatedEmail = new TemplatedEmail();
        $templatedEmail->subject($subject);
        $templatedEmail->locale($locale);
        $templatedEmail->htmlTemplate($this->template);
        $templatedEmail->context([...['locale' => $locale], ...$context]);

        $recipient = new Recipient($event->getAccount()->getEmail()->toString());
        $notification = new TemplatedEmailNotification($templatedEmail);

        $this->notifier->send($notification, $recipient);
    }
}
