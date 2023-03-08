<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\Auth\GetSigninAccountInfoHandler;
use App\Domain\Message\Auth\GetSigninAccountInfoQuery;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

final class GetSigninAccountInfoHandlerTest extends Unit
{
    private GetSigninAccountInfoHandler $getSigninAccountInfoHandler;

    private Security&MockObject $security;

    private UserInterface&MockObject $user;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->user = $this->createMock(originalClassName: UserInterface::class);
        $this->security = $this->createMock(originalClassName: Security::class);
        $this->getSigninAccountInfoHandler = new GetSigninAccountInfoHandler($this->security);
    }

    public function testInvokeWithInvalidUser(): void
    {
        $this->security
            ->expects($this->once())
            ->method(constraint: 'getUser')
            ->willReturn(value: null);
        $this->expectException(AccessDeniedHttpException::class);

        ($this->getSigninAccountInfoHandler)(new GetSigninAccountInfoQuery());
    }

    /**
     * @throws Exception
     */
    public function testInvokeWithValidUser(): void
    {
        $this->security
            ->expects($this->once())
            ->method(constraint: 'getUser')
            ->willReturn($this->user);

        $user = ($this->getSigninAccountInfoHandler)(new GetSigninAccountInfoQuery());

        $this->assertSame($this->user, $user);
    }
}
