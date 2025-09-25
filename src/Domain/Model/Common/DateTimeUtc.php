<?php

declare(strict_types=1);

namespace App\Domain\Model\Common;

use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class DateTimeUtc
{
    private const string TIMEZONE = 'UTC';

    final protected function __construct(
        private readonly DatePoint $datetime,
    ) {
    }

    public static function now(): self
    {
        return new self(new DatePoint()->setTimezone(new DateTimeZone(self::TIMEZONE)));
    }

    public static function fromInterface(DateTimeInterface $datetime): self
    {
        return new self(DatePoint::createFromInterface($datetime)->setTimezone(new DateTimeZone(self::TIMEZONE)));
    }

    public function toImmutable(): DatePoint
    {
        return $this->datetime;
    }

    public function toAtomString(): string
    {
        return $this->datetime->format(DateTimeInterface::ATOM);
    }
}
