<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountCreateNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private readonly string $accountEmail,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct(channels: ['email']);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $domain = 'notifications+intl-icu';
        $subject = 'account.create.email.subject';
        $content = 'account.create.email.message';
        $locale = $this->translator->getLocale();
        $context = [
            'account_email' => $this->accountEmail,
        ];

        $email = NotificationEmail::asPublicEmail();
        $email->theme(theme: 'default');
        $email->to($recipient->getEmail());
        $email->context([...$context, ...['locale' => $locale]]);
        $email->content($this->translator->trans($content, $context, $domain, $locale));
        $email->subject($this->translator->trans($subject, $context, $domain, $locale));

        return new EmailMessage($email);
    }
}
