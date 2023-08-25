<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\SigninIntoAccount\SigninIntoAccountCommand;
use App\Application\Handler\SigninIntoAccount\SigninIntoAccountHandler;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SigninIntoAccountHandlerTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(originalClassName: TokenStorageInterface::class);

        $this->signinIntoAccountHandler = new SigninIntoAccountHandler($this->tokenStorage);
    }

    public function testInvokeThrowsExceptionOnAccessDenied(): void
    {
        $message = new SigninIntoAccountCommand();

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedHttpException::class);

        ($this->signinIntoAccountHandler)($message);
    }
}
