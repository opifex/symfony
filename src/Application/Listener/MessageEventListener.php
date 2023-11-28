<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: MessageEvent::class)]
final class MessageEventListener
{
    private const string TRANSLATOR_DOMAIN = 'notifications';

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function __invoke(MessageEvent $event): void
    {
        $message = $event->getMessage();

        if ($message instanceof NotificationEmail) {
            $subject = $message->getSubject() ?? '';
            $content = $message->getContext()['content'] ?? '';
            $locale = $message->getContext()['locale'] ?? $this->translator->getLocale();
            $domain = self::TRANSLATOR_DOMAIN . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

            $message->subject($this->translator->trans($subject, $message->getContext(), $domain, $locale));
            $message->content($this->translator->trans($content, $message->getContext(), $domain, $locale));
            $message->context([...$message->getContext(), ...['locale' => $locale]]);
        }
    }
}
