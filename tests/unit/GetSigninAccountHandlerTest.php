<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\GetSigninAccountHandler;
use App\Domain\Message\GetSigninAccountQuery;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetSigninAccountHandlerTest extends Unit
{
    private GetSigninAccountHandler $getSigninAccountHandler;

    private TokenStorageInterface&MockObject $tokenStorage;

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
        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedHttpException::class);

        ($this->getSigninAccountHandler)(new GetSigninAccountQuery());
    }
}
