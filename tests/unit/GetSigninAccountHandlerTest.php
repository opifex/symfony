<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\GetSigninAccountHandler;
use App\Domain\Message\GetSigninAccountQuery;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class GetSigninAccountHandlerTest extends Unit
{
    private GetSigninAccountHandler $getSigninAccountHandler;

    private Security&MockObject $security;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->security = $this->createMock(originalClassName: Security::class);
        $this->getSigninAccountHandler = new GetSigninAccountHandler($this->security);
    }

    public function testInvokeWithInvalidUser(): void
    {
        $this->security
            ->expects($this->once())
            ->method(constraint: 'getUser')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedHttpException::class);

        ($this->getSigninAccountHandler)(new GetSigninAccountQuery());
    }
}
