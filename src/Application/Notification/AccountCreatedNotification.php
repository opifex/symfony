<?php

declare(strict_types=1);

namespace App\Application\Notification;

use App\Domain\Contract\AccountInterface;
use Override;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountCreatedNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private AccountInterface $account,
        private TranslatorInterface $translator,
    ) {
        parent::__construct();
    }

    #[Override]
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = new TemplatedEmail();
        $email->to($recipient->getEmail());
        $email->locale($this->account->getLocale());
        $email->subject($this->translator->trans(
            id: 'Thank you for registration',
            locale: $this->account->getLocale(),
        ));
        $email->htmlTemplate(template: '@emails/account.created.html.twig');
        $email->context([
            'locale' => $this->account->getLocale(),
            'account' => ['email' => $this->account->getEmail()],
        ]);

        return new EmailMessage($email);
    }
}
