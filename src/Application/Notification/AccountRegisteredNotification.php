<?php

declare(strict_types=1);

namespace App\Application\Notification;

use App\Domain\Model\Account;
use Override;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Exclude]
final class AccountRegisteredNotification extends Notification implements EmailNotificationInterface
{
    private string $subject = 'Thank you for registration';

    public function __construct(
        private readonly Account $account,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct();
    }

    #[Override]
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): EmailMessage
    {
        $email = new TemplatedEmail();
        $email->to($recipient->getEmail());
        $email->locale($this->account->getLocale()->toString());
        $email->subject($this->translator->trans($this->subject, locale: $this->account->getLocale()->toString()));
        $email->htmlTemplate(template: '@emails/account.registered.html.twig');
        $email->context([
            'locale' => $this->account->getLocale()->toString(),
            'account' => ['email' => $this->account->getEmail()->toString()],
        ]);

        return new EmailMessage($email);
    }
}
