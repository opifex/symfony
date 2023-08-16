<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Translation\LocaleSwitcher;

final class LocaleMiddleware implements MiddlewareInterface
{
    public function __construct(private LocaleSwitcher $localeSwitcher)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $localeStamp = $envelope->last(stampFqcn: LocaleStamp::class);
        $localeStamp ??= new LocaleStamp($this->localeSwitcher->getLocale());
        $envelope = $envelope->withoutAll($localeStamp::class)->with($localeStamp);

        $this->localeSwitcher->setLocale($localeStamp->getLocale());

        return $stack->next()->handle($envelope, $stack);
    }
}
