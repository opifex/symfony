<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use Override;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

final class TemplatedEmailNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private readonly TemplatedEmail $templatedEmail,
    ) {
        parent::__construct();
    }

    #[Override]
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): EmailMessage
    {
        $this->templatedEmail->to(Address::create($recipient->getEmail()));

        return new EmailMessage($this->templatedEmail);
    }
}
