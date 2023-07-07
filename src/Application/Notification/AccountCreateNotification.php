<?php

declare(strict_types=1);

namespace App\Application\Notification;

use App\Domain\Contract\AccountInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

final class AccountCreateNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(private AccountInterface $account, array $channels = [])
    {
        parent::__construct(channels: $channels);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = NotificationEmail::asPublicEmail();
        $email->to($recipient->getEmail());
        $email->subject(subject: 'account.create.email.subject');
        $email->content(content: 'account.create.email.content');
        $email->context(['account_email' => $this->account->getEmail()]);

        return new EmailMessage($email);
    }
}
