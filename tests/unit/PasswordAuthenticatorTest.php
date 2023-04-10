<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\PasswordAuthenticator;
use App\Domain\Contract\Adapter\JwtTokenAdapterInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final class PasswordAuthenticatorTest extends Unit
{
    private PasswordAuthenticator $passwordAuthenticator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $jwtTokenAdapter = $this->createMock(originalClassName: JwtTokenAdapterInterface::class);
        $this->passwordAuthenticator = new PasswordAuthenticator($jwtTokenAdapter);
    }

    public function testAuthenticateThrowsExceptionOnEmptyCredentials(): void
    {
        $this->expectException(BadCredentialsException::class);

        $this->passwordAuthenticator->authenticate(new Request());
    }
}
