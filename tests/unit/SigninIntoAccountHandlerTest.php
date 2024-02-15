<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Handler\SigninIntoAccount\SigninIntoAccountCommand;
use App\Application\Handler\SigninIntoAccount\SigninIntoAccountHandler;
use App\Domain\Exception\AccessDeniedException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SigninIntoAccountHandlerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(originalClassName: TokenStorageInterface::class);
        $this->eventDispatcher = $this->createMock(originalClassName: EventDispatcherInterface::class);
    }

    public function testInvokeThrowsExceptionOnAccessDenied(): void
    {
        $signinIntoAccountHandler = new SigninIntoAccountHandler($this->tokenStorage);

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(AccessDeniedException::class);

        ($signinIntoAccountHandler)(new SigninIntoAccountCommand());
    }
}
