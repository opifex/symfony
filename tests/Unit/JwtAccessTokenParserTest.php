<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Adapter\Lcobucci\Exception\InvalidTokenException;
use App\Infrastructure\Adapter\Lcobucci\JwtAccessTokenIssuer;
use App\Infrastructure\Adapter\Lcobucci\JwtAccessTokenParser;
use App\Infrastructure\Adapter\Lcobucci\JwtConfigurationBag;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class JwtAccessTokenParserTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->jwtConfigurationBag = new JwtConfigurationBag(
            issuer: 'https://example.com',
            lifetime: 1,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
        );
    }

    public function testDecodeAccessTokenThrowsExceptionWithEmptyAccessToken(): void
    {
        $jwtAccessTokenParser = new JwtAccessTokenParser($this->jwtConfigurationBag);

        $this->expectException(InvalidTokenException::class);

        $jwtAccessTokenParser->parse(accessToken: '');
    }

    public function testDecodeAccessTokenThrowsExceptionWithInvalidTokenStructure(): void
    {
        $jwtAccessTokenParser = new JwtAccessTokenParser($this->jwtConfigurationBag);

        $this->expectException(InvalidTokenException::class);

        $jwtAccessTokenParser->parse(accessToken: 'invalid');
    }

    public function testDecodeAccessTokenThrowsExceptionWithInvalidTokenContent(): void
    {
        $jwtAccessTokenParser = new JwtAccessTokenParser($this->jwtConfigurationBag);

        $tokenString = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYmYiOjE2N';
        $tokenString .= '.6fwOHO3K4mnu0r_TQU0QUn1OkphV84LdSHBNGOGhbCQ';

        $this->expectException(InvalidTokenException::class);

        $jwtAccessTokenParser->parse($tokenString);
    }

    public function testParseExtractsTokenIdentifierAndExpiresAt(): void
    {
        $clock = new MockClock('2026-01-01T00:00:00+00:00');
        $jwtConfigurationBag = new JwtConfigurationBag(
            issuer: 'https://example.com',
            lifetime: 3600,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            clock: $clock,
        );
        $tokenString = (new JwtAccessTokenIssuer($jwtConfigurationBag))
            ->issue(userIdentifier: 'user-123', userRoles: ['ROLE_USER']);

        $result = (new JwtAccessTokenParser($jwtConfigurationBag))->parse(accessToken: $tokenString);

        self::assertMatchesRegularExpression(
            pattern: '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            string: $result->identifier,
        );
        self::assertEquals(
            expected: new \DateTimeImmutable('2026-01-01T01:00:00+00:00'),
            actual: $result->expiresAt,
        );
        self::assertSame('user-123', $result->userIdentifier);
    }
}
