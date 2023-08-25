<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\GetSigninAccount\GetSigninAccountHandler;
use App\Application\Handler\GetSigninAccount\GetSigninAccountQuery;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetSigninAccountHandlerTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(originalClassName: TokenStorageInterface::class);

        $this->getSigninAccountHandler = new GetSigninAccountHandler($this->tokenStorage);
    }

    public function testInvokeWithInvalidUser(): void
    {
        $message = new GetSigninAccountQuery();

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedHttpException::class);

        ($this->getSigninAccountHandler)($message);
    }
}
