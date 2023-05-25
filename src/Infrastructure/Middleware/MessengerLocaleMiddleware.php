<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Domain\Messenger\LocaleStamp;
use LogicException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Translation\LocaleSwitcher;

final class MessengerLocaleMiddleware implements MiddlewareInterface
{
    public function __construct(private LocaleSwitcher $localeSwitcher)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $currentLocale = $this->localeSwitcher->getLocale();

        try {
            $this->localeSwitcher->setLocale($this->extractLocale($envelope));
        } catch (LogicException) {
            $envelope = $envelope->with(new LocaleStamp($currentLocale));
        }

        try {
            return $stack->next()->handle($envelope, $stack);
        } finally {
            $this->localeSwitcher->setLocale($currentLocale);
        }
    }

    private function extractLocale(Envelope $envelope): string
    {
        $localeStamp = $envelope->last(stampFqcn: LocaleStamp::class);

        if (!$localeStamp instanceof LocaleStamp) {
            throw new LogicException(message: 'No locale stamp found in message.');
        }

        return $localeStamp->getLocale();
    }
}
