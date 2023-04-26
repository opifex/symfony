<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractNotification extends Notification implements EmailNotificationInterface
{
    final public const CHANNEL_EMAIL = 'email';

    protected string $theme = 'default';

    private readonly string $translationAlias;

    private readonly ?string $translationLocale;

    public function __construct(
        private array $channels = [],
        private ?string $locale = null,
        private ?TranslatorInterface $translator = null,
    ) {
        $this->translationAlias = $this->getTranslationAlias($this::class);
        $this->translationLocale = $this->getTranslationLocale($this->locale, $this->translator);

        parent::__construct(channels: $this->channels);
    }

    /**
     * @throws ExceptionInterface
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $context = $this->getContext();

        $email = new NotificationEmail();
        $email->content($this->getTranslation(key: 'email.message', parameters: $context));
        $email->context([...$context, ...['locale' => $this->translationLocale]]);
        $email->markAsPublic();
        $email->subject($this->getTranslation(key: 'email.subject', parameters: $context));
        $email->theme($this->theme);
        $email->to($recipient->getEmail());

        return new EmailMessage($email);
    }

    /**
     * @return array<string, mixed>
     * @throws ExceptionInterface
     */
    private function getContext(): array
    {
        $nameConverter = new CamelCaseToSnakeCaseNameConverter();
        $normalized = (new ObjectNormalizer(nameConverter: $nameConverter))->normalize($this);
        $protectedKeys = ['content', 'emoji', 'emoji', 'exception', 'exception_as_string', 'importance', 'subject'];

        return array_filter(
            array: is_array($normalized) ? $normalized : [],
            callback: fn($key) => !in_array($key, $protectedKeys),
            mode: ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * @param string $key
     * @param array&array<string, mixed> $parameters
     *
     * @return string
     */
    private function getTranslation(string $key, array $parameters = []): string
    {
        $key = sprintf('%s.%s', $this->translationAlias, $key);

        if ($this->translator instanceof TranslatorInterface) {
            $key = $this->translator->trans(
                id: $key,
                parameters: $parameters,
                domain: 'notifications+intl-icu',
                locale: $this->translationLocale,
            );
        }

        return $key;
    }

    private function getTranslationAlias(string $className): string
    {
        return (new UnicodeString($className))
            ->afterLast(needle: '\\')
            ->snake()
            ->beforeLast(needle: '_')
            ->replace('_', '.')
            ->toString();
    }

    private function getTranslationLocale(?string $locale, ?TranslatorInterface $translator): ?string
    {
        return $locale === null && $translator ? $translator->getLocale() : $locale;
    }
}
