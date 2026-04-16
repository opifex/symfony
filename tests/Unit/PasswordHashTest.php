<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Foundation\ValueObject\PasswordHash;
use DomainException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class PasswordHashTest extends TestCase
{
    public function testPlainPasswordThrowsDomainException(): void
    {
        $this->expectException(DomainException::class);

        PasswordHash::fromString(passwordHash: 'plaintext');
    }

    public function testEmptyStringThrowsDomainException(): void
    {
        $this->expectException(DomainException::class);

        PasswordHash::fromString(passwordHash: '');
    }

    #[DataProvider(methodName: 'validHashProvider')]
    public function testValidHashIsAccepted(string $hash): void
    {
        $passwordHash = PasswordHash::fromString(passwordHash: $hash);

        self::assertSame($hash, $passwordHash->toString());
    }

    public function testHashWithSurroundingSpacesIsTrimmed(): void
    {
        $hash = '$2y$12$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234';
        $passwordHash = PasswordHash::fromString(passwordHash: " {$hash} ");

        self::assertSame($hash, $passwordHash->toString());
    }

    public static function validHashProvider(): iterable
    {
        yield 'bcrypt $2y hash' => ['$2y$12$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234'];
        yield 'bcrypt $2a hash' => ['$2a$10$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234'];
        yield 'bcrypt $2b hash' => ['$2b$10$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234'];
        yield 'argon2i hash' => ['$argon2i$v=19$m=65536,t=4,p=1$c29tZXNhbHQ$RdescudvJCsgt3ub+b+dWRWJTmaaJObG'];
        yield 'argon2id hash' => ['$argon2id$v=19$m=65536,t=4,p=1$c29tZXNhbHQ$RdescudvJCsgt3ub+b+dWRWJTmaaJObG'];
    }
}
