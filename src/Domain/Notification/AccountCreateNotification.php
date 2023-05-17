<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

final class AccountCreateNotification extends Notification implements EmailNotificationInterface
{
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = NotificationEmail::asPublicEmail();
        $email->to($recipient->getEmail());
        $email->subject(subject: 'account.create.email.subject');
        $email->content(content: 'account.create.email.content');
        $email->context(['account_email' => $recipient->getEmail()]);

        return new EmailMessage($email);
    }
}
