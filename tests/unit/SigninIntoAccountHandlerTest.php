<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\Auth\SigninIntoAccountHandler;
use App\Domain\Message\Auth\SigninIntoAccountCommand;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class SigninIntoAccountHandlerTest extends Unit
{
    private SigninIntoAccountHandler $signinIntoAccountHandler;

    private Security&MockObject $security;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->security = $this->createMock(originalClassName: Security::class);
        $this->signinIntoAccountHandler = new SigninIntoAccountHandler($this->security);
    }

    public function testInvokeThrowsExceptionOnAccessDenied(): void
    {
        $this->security
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedHttpException::class);

        ($this->signinIntoAccountHandler)(new SigninIntoAccountCommand());
    }
}
