<?php

declare(strict_types=1);

namespace App\Domain\Model\Common;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class DateTimeUtc
{
    private const string TIMEZONE = 'UTC';

    final protected function __construct(
        private readonly DateTimeImmutable $datetime,
    ) {
    }

    public static function now(): self
    {
        return new self(new DateTimeImmutable()->setTimezone(new DateTimeZone(self::TIMEZONE)));
    }

    public static function fromImmutable(DateTimeImmutable $datetime): self
    {
        return new self($datetime->setTimezone(new DateTimeZone(self::TIMEZONE)));
    }

    public function toImmutable(): DateTimeImmutable
    {
        return $this->datetime;
    }

    public function toAtomString(): string
    {
        return $this->datetime->format(DateTimeInterface::ATOM);
    }
}
