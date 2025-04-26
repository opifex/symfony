<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Model\AccountRole;
use App\Infrastructure\Security\PasswordAuthenticatedUser;
use App\Infrastructure\Security\PasswordAuthenticatedUserChecker;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final class PasswordAuthenticatedUserCheckerTest extends Unit
{
    private UserInterface&MockObject $user;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->user = $this->createMock(type: UserInterface::class);
    }

    public function testCheckPostAuthWithBlockedAccount(): void
    {
        $accountUserChecker = new PasswordAuthenticatedUserChecker();
        $accountUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::USER],
            enabled: false,
        );

        $this->expectException(CustomUserMessageAccountStatusException::class);

        $accountUserChecker->checkPostAuth($accountUser);
    }

    public function testCheckPostAuthWithNonAccountUser(): void
    {
        $accountUserChecker = new PasswordAuthenticatedUserChecker();
        $accountUserChecker->checkPostAuth($this->user);

        $this->expectNotToPerformAssertions();
    }

    public function testCheckPostAuthWithVerifiedAccount(): void
    {
        $accountUserChecker = new PasswordAuthenticatedUserChecker();
        $accountUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::USER],
            enabled: true,
        );
        $accountUserChecker->checkPostAuth($accountUser);

        $this->expectNotToPerformAssertions();
    }
}
