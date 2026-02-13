<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Foundation\ValueObject\EmailAddress;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
final class EmailAddressTest extends TestCase
{
    public function testInvalidEmailThrowsDomainException(): void
    {
        $this->expectException(DomainException::class);

        EmailAddress::fromString(email: 'example.com');
    }

    #[DataProvider(methodName: 'emailAddressProvider')]
    public function testNormalizeWithDifferentTypes(mixed $value, mixed $expected): void
    {
        $emailAddress = EmailAddress::fromString($value);

        $this->assertSame($expected, $emailAddress->toString());
    }

    public static function emailAddressProvider(): iterable
    {
        yield 'already normalized email' => ['value' => 'email@example.com', 'expected' => 'email@example.com'];
        yield 'email with surrounding spaces' => ['value' => ' email@example.com ', 'expected' => 'email@example.com'];
        yield 'email with uppercase characters' => ['value' => 'Email@example.com ', 'expected' => 'email@example.com'];
    }
}
