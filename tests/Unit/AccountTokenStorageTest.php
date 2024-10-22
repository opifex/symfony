<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\AccountTokenStorage;
use App\Domain\Exception\AccountUnauthorizedException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AccountTokenStorageTest extends Unit
{
    private TokenStorageInterface&MockObject $tokenStorage;

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
        $accountAuthorizationFetcher = new AccountTokenStorage($this->tokenStorage);

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AccountUnauthorizedException::class);

        $accountAuthorizationFetcher->getAccount();
    }
}
