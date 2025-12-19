<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\Clock\DatePoint;

final class DateTimeUtc
{
    private const string TIMEZONE = 'UTC';

    final private function __construct(
        private readonly DatePoint $datetime,
    ) {
    }

    public static function now(): self
    {
        return new self(
            new DatePoint()->setTimezone(
                timezone: new DateTimeZone(timezone: self::TIMEZONE),
            ),
        );
    }

    public static function fromInterface(DateTimeInterface $datetime): self
    {
        return new self(
            DatePoint::createFromInterface($datetime)->setTimezone(
                timezone: new DateTimeZone(timezone: self::TIMEZONE),
            ),
        );
    }

    public function toImmutable(): DatePoint
    {
        return $this->datetime;
    }

    public function toAtomString(): string
    {
        return $this->datetime->format(format: DateTimeInterface::ATOM);
    }
}
