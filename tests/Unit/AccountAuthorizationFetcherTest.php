<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\Service\AccountAuthorizationFetcher;
use App\Domain\Exception\AccountUnauthorizedException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AccountAuthorizationFetcherTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(originalClassName: TokenStorageInterface::class);
    }

    public function testFetchAccountThrowsExceptionWithUnauthorizedUser(): void
    {
        $accountAuthorizationFetcher = new AccountAuthorizationFetcher($this->tokenStorage);

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AccountUnauthorizedException::class);

        $accountAuthorizationFetcher->fetchAccount();
    }

    public function testFetchTokenThrowsExceptionWithUnauthorizedUser(): void
    {
        $accountAuthorizationFetcher = new AccountAuthorizationFetcher($this->tokenStorage);

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AccountUnauthorizedException::class);

        $accountAuthorizationFetcher->fetchToken();
    }
}
