<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\SigninIntoAccountHandler;
use App\Domain\Message\SigninIntoAccountCommand;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SigninIntoAccountHandlerTest extends Unit
{
    private SigninIntoAccountHandler $signinIntoAccountHandler;

    private TokenStorageInterface&MockObject $tokenStorage;

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
        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedHttpException::class);

        ($this->signinIntoAccountHandler)(new SigninIntoAccountCommand());
    }
}
