<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Adapter\Lcobucci\Exception\InvalidTokenException;
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
        $this->clock = new MockClock();
    }

    public function testDecodeAccessTokenThrowsExceptionWithEmptyAccessToken(): void
    {
        $jwtConfigurationBag = new JwtConfigurationBag(
            issuer: 'https://example.com',
            lifetime: 1,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
        );
        $jwtAccessTokenParser = new JwtAccessTokenParser($jwtConfigurationBag);

        $this->expectException(InvalidTokenException::class);

        $jwtAccessTokenParser->parse(accessToken: '');
    }

    public function testDecodeAccessTokenThrowsExceptionWithInvalidTokenStructure(): void
    {
        $jwtConfigurationBag = new JwtConfigurationBag(
            issuer: 'https://example.com',
            lifetime: 1,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
        );
        $jwtAccessTokenParser = new JwtAccessTokenParser($jwtConfigurationBag);

        $this->expectException(InvalidTokenException::class);

        $jwtAccessTokenParser->parse(accessToken: 'invalid');
    }

    public function testDecodeAccessTokenThrowsExceptionWithInvalidTokenContent(): void
    {
        $jwtConfigurationBag = new JwtConfigurationBag(
            issuer: 'https://example.com',
            lifetime: 1,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
        );
        $jwtAccessTokenParser = new JwtAccessTokenParser($jwtConfigurationBag);

        $tokenString = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYmYiOjE2N';
        $tokenString .= '.6fwOHO3K4mnu0r_TQU0QUn1OkphV84LdSHBNGOGhbCQ';

        $this->expectException(InvalidTokenException::class);

        $jwtAccessTokenParser->parse($tokenString);
    }
}
