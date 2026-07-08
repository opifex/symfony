<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Foundation\ValueObject\DateTimeUtc;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class DateTimeUtcTest extends TestCase
{
    public function testEqualsReturnsTrueForSameValueInDifferentInstance(): void
    {
        $datetime = DateTimeUtc::fromInterface(datetime: new DateTimeImmutable(datetime: '2026-01-01 12:00:00'));
        $other = new DateTimeImmutable(datetime: '2026-01-01 12:00:00');

        $this->assertTrue($datetime->equals($other));
    }

    public function testEqualsReturnsFalseForDifferentValue(): void
    {
        $datetime = DateTimeUtc::fromInterface(datetime: new DateTimeImmutable(datetime: '2026-01-01 12:00:00'));
        $other = new DateTimeImmutable(datetime: '2026-01-01 12:00:01');

        $this->assertFalse($datetime->equals($other));
    }

    public function testEqualsReturnsFalseForNull(): void
    {
        $datetime = DateTimeUtc::fromInterface(datetime: new DateTimeImmutable(datetime: '2026-01-01 12:00:00'));

        $this->assertFalse($datetime->equals(datetime: null));
    }
}
