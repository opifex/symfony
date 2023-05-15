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

final class GetSigninAccountInfoHandlerTest extends Unit
{
    private GetSigninAccountInfoHandler $getSigninAccountInfoHandler;

    private Security&MockObject $security;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
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
}
