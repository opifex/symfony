<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class LocaleStamp implements StampInterface
{
    public function __construct(private string $locale)
    {
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
