<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: MessageEvent::class)]
final class MessageListener
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function __invoke(MessageEvent $event): void
    {
        $message = $event->getMessage();

        if ($message instanceof NotificationEmail) {
            $domain = 'notifications+intl-icu';
            $subject = $message->getSubject() ?? '';
            $content = $message->getContext()['content'] ?? '';
            $locale = $message->getContext()['locale'] ?? $this->translator->getLocale();

            $message->subject($this->translator->trans($subject, $message->getContext(), $domain, $locale));
            $message->content($this->translator->trans($content, $message->getContext(), $domain, $locale));
            $message->context([...$message->getContext(), ...['locale' => $locale]]);
        }
    }
}
