<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\GetSigninAccount\GetSigninAccountHandler;
use App\Application\Handler\GetSigninAccount\GetSigninAccountQuery;
use App\Domain\Exception\AccessDeniedException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class GetSigninAccountHandlerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(originalClassName: TokenStorageInterface::class);
    }

    public function testInvokeWithInvalidUser(): void
    {
        $getSigninAccountHandler = new GetSigninAccountHandler($this->tokenStorage);

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedException::class);

        ($getSigninAccountHandler)(new GetSigninAccountQuery());
    }
}
