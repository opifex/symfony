<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\PasswordAuthenticator;
use App\Domain\Contract\Adapter\JwtAdapterInterface;
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
        $jwtAdapter = $this->createMock(originalClassName: JwtAdapterInterface::class);
        $this->passwordAuthenticator = new PasswordAuthenticator($jwtAdapter);
    }

    public function testAuthenticateThrowsExceptionOnEmptyCredentials(): void
    {
        $this->expectException(BadCredentialsException::class);

        $this->passwordAuthenticator->authenticate(new Request());
    }
}
